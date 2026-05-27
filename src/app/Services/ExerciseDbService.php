<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExerciseDbService
{
    protected string $baseUrl = 'https://oss.exercisedb.dev/api/v1';

    // ──────────────────────────────────────────────────────────────
    //  Busca 1 exercício pelo nome  (modal de guia)
    // ──────────────────────────────────────────────────────────────
    public function searchByName(string $name): ?array
    {
        $results = $this->searchMultiple($name, 1);
        return $results[0] ?? null;
    }

    // ──────────────────────────────────────────────────────────────
    //  Busca múltiplos  (autocomplete)
    //
    //  A API retorna { success, meta: { total, nextCursor, ... }, data: [...] }
    //  O endpoint /search com ?name= retorna data: [] sempre, então
    //  usamos /exercises paginado + filtro PHP.
    // ──────────────────────────────────────────────────────────────
    public function searchMultiple(string $query, int $limit = 12): array
    {
        $cacheKey = 'exercisedb_multi_' . md5(strtolower(trim($query)) . $limit);

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($query, $limit) {
            return $this->listAndFilter($query, $limit);
        });
    }

    // ──────────────────────────────────────────────────────────────
    //  Debug  (somente local — remove em produção)
    // ──────────────────────────────────────────────────────────────
    public function rawDebug(string $query): array
    {
        $out = [];

        try {
            $r = Http::timeout(10)->get("{$this->baseUrl}/exercises/search", [
                'name' => $query, 'limit' => 3,
            ]);
            $out['search_name'] = ['status' => $r->status(), 'body' => $r->json()];
        } catch (\Throwable $e) {
            $out['search_name'] = ['error' => $e->getMessage()];
        }

        try {
            $r = Http::timeout(10)->get("{$this->baseUrl}/exercises", ['limit' => 3]);
            $out['list'] = ['status' => $r->status(), 'body' => $r->json()];
        } catch (\Throwable $e) {
            $out['list'] = ['error' => $e->getMessage()];
        }

        return $out;
    }

    // ──────────────────────────────────────────────────────────────
    //  Busca por ID
    // ──────────────────────────────────────────────────────────────
    public function findById(string $exerciseId): ?array
    {
        return Cache::remember("exercisedb_id_{$exerciseId}", now()->addHours(24), function () use ($exerciseId) {
            try {
                $r = Http::timeout(10)->get("{$this->baseUrl}/exercises/{$exerciseId}");
                if (!$r->successful()) return null;
                $data = $r->json();
                return $data['data'] ?? $data;
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    // ──────────────────────────────────────────────────────────────
    //  PRIVADOS
    // ──────────────────────────────────────────────────────────────

    /**
     * Pagina /exercises com cursor e filtra pelo nome no PHP.
     * Para no $limit ou quando não há mais páginas ($maxPages = segurança).
     */
    private function listAndFilter(string $query, int $limit, int $maxPages = 5): array
    {
        $q      = strtolower(trim($query));
        $found  = [];
        $cursor = null;

        for ($page = 0; $page < $maxPages; $page++) {
            $params = ['limit' => 100];
            if ($cursor) $params['cursor'] = $cursor;

            try {
                $r = Http::timeout(12)->get("{$this->baseUrl}/exercises", $params);
                if (!$r->successful()) break;

                $body      = $r->json();
                $exercises = $body['data'] ?? [];
                if (empty($exercises)) break;

                foreach ($exercises as $ex) {
                    if (str_contains(strtolower($ex['name'] ?? ''), $q)) {
                        $found[] = $ex;
                        if (count($found) >= $limit) return $found;
                    }
                }

                $cursor = $body['meta']['nextCursor'] ?? null;
                if (!$cursor || !($body['meta']['hasNextPage'] ?? false)) break;

            } catch (\Throwable $e) {
                Log::debug('ExerciseDB list failed: ' . $e->getMessage());
                break;
            }
        }

        return $found;
    }
}