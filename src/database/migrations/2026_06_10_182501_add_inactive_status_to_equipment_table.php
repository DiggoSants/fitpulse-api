<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('equipment', 'description')) {
            Schema::table('equipment', function (Blueprint $table) {
                $description = $table->text('description')->nullable();

                if (Schema::hasColumn('equipment', 'unique_code')) {
                    $description->after('unique_code');
                } else {
                    $description->after('name');
                }
            });
        }

        $this->fillMissingUniqueCodes();
        $this->setMysqlStatusEnum(['ativo', 'manutencao', 'inativo']);
    }

    public function down(): void
    {
        DB::table('equipment')
            ->where('status', 'inativo')
            ->update(['status' => 'ativo']);

        $this->setMysqlStatusEnum(['ativo', 'manutencao']);

        if (Schema::hasColumn('equipment', 'description')) {
            Schema::table('equipment', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }

    private function fillMissingUniqueCodes(): void
    {
        if (!Schema::hasColumn('equipment', 'unique_code')) {
            return;
        }

        $equipment = DB::table('equipment')
            ->whereNull('unique_code')
            ->orWhere('unique_code', '')
            ->orderBy('id')
            ->get(['id']);

        foreach ($equipment as $item) {
            DB::table('equipment')
                ->where('id', $item->id)
                ->update([
                    'unique_code' => '#EQ-'.now()->format('Ymd').'-'.str_pad((string) $item->id, 3, '0', STR_PAD_LEFT),
                ]);
        }
    }

    private function setMysqlStatusEnum(array $values): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $enumValues = collect($values)
            ->map(fn (string $value) => DB::getPdo()->quote($value))
            ->implode(',');

        DB::statement("ALTER TABLE equipment MODIFY COLUMN status ENUM({$enumValues}) NOT NULL DEFAULT 'ativo'");
    }
};
