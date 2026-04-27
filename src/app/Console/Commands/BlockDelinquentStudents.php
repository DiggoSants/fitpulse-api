<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Billing;
use Carbon\Carbon;

class BlockDelinquentStudents extends Command
{
    protected $signature   = 'students:block-delinquent';
    protected $description = 'Bloqueia alunos que renovaram há mais de 1 dia e não pagaram.';

    public function handle(): void
    {
        $delinquents = Student::where('status', 'active')
            ->whereNotNull('renewed_at')
            ->where('renewed_at', '<=', Carbon::now()->subDay())
            ->whereHas('billings', function ($q) {
                $q->where('status', 'pending')
                    ->whereNull('paid_at');
            })
            ->get();

        $count = 0;

        foreach ($delinquents as $student) {
            $student->markDelinquent();
            $count++;

            $this->line("Bloqueado: {$student->user->name} (ID: {$student->id})");
        }

        $this->info("Total de alunos bloqueados: {$count}");
    }
}