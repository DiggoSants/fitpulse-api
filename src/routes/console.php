<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Roda todo dia às 08:00 — bloqueia alunos que renovaram há mais de 1 dia sem pagar
Schedule::command('students:block-delinquent')->dailyAt('08:00');