<?php

namespace App\Http\Controllers;

use App\Services\ExerciseDbService;
use Illuminate\Http\Request;

class ExerciseApiController extends Controller
{
    public function __construct(protected ExerciseDbService $service) {}

    // ──────────────────────────────────────────────────────────────
    //  Modal de guia: 1 exercício pelo nome
    // ──────────────────────────────────────────────────────────────
    public function guide(Request $request)
    {
        $name = trim($request->query('name', ''));
        if (empty($name)) {
            return response()->json(['error' => 'Nome não informado.'], 422);
        }

        $ex = $this->service->searchByName($name);

        if (!$ex) {
            return response()->json(['error' => 'Exercício não encontrado na API.'], 404);
        }

        return response()->json([
            'id'                => $ex['exerciseId']       ?? null,
            'name'              => $ex['name']             ?? $name,
            'gif_url'           => $ex['gifUrl']           ?? null,   // URL completa: https://static.exercisedb.dev/media/xxx.gif
            'target_muscles'    => $ex['targetMuscles']    ?? [],
            'secondary_muscles' => $ex['secondaryMuscles'] ?? [],
            'body_parts'        => $ex['bodyParts']        ?? [],
            'equipments'        => $ex['equipments']       ?? [],
            'instructions'      => $ex['instructions']     ?? [],
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  Autocomplete: vários exercícios pelo nome
    // ──────────────────────────────────────────────────────────────
    public function search(Request $request)
    {
        $query = trim($request->query('q', ''));
        $limit = min((int) $request->query('limit', 12), 30);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = $this->service->searchMultiple($query, $limit);

        $items = collect($results)
            ->map(fn($ex) => [
                'id'           => $ex['exerciseId']    ?? null,
                'name'         => $ex['name']          ?? '',
                'muscle_group' => $this->primaryMuscle($ex),
                'body_part'    => $ex['bodyParts'][0]  ?? '',
                'equipment'    => $ex['equipments'][0] ?? '',
                'gif_url'      => $ex['gifUrl']        ?? null,
            ])
            ->filter(fn($ex) => !empty($ex['name']))
            ->values();

        return response()->json($items);
    }

    // ──────────────────────────────────────────────────────────────
    //  Debug — resposta bruta. Rota SEM middleware, só local.
    //  REMOVA antes de fazer deploy.
    // ──────────────────────────────────────────────────────────────
    public function debug(Request $request)
    {
        if (app()->isProduction()) abort(404);

        $q = $request->query('q', 'bench');
        return response()->json(
            $this->service->rawDebug($q),
            200,
            ['Content-Type' => 'application/json'],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    // ──────────────────────────────────────────────────────────────
    private function primaryMuscle(array $ex): string
    {
        $targets = $ex['targetMuscles'] ?? [];
        if (!empty($targets)) return ucfirst($targets[0]);
        return ucfirst($ex['bodyParts'][0] ?? '');
    }
}