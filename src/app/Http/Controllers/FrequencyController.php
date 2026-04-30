<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Frequency;

class FrequencyController extends Controller
{
    public function register()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Apenas alunos podem registrar presença.',
            ], 403);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        if (!$student->hasAccess()) {
            $message = $student->isBlocked()
                ? 'Seu acesso está bloqueado. Entre em contato com a academia.'
                : 'Seu acesso está suspenso por inadimplência. Regularize seu pagamento.';

            return response()->json(['message' => $message], 403);
        }

        if (!$student->isEnrolled()) {
            return response()->json([
                'message' => 'Você não possui matrícula ativa.',
            ], 403);
        }

        $frequency = Frequency::create([
            'student_id' => $student->id,
        ]);

        return response()->json([
            'message'    => 'Presença registrada com sucesso!',
            'data'       => [
                'id'         => $frequency->id,
                'registered_at' => $frequency->created_at->format('d/m/Y H:i:s'),
            ],
        ], 201);
    }

public function heatmap()
{
    $frequencies = Frequency::selectRaw("
            DAYOFWEEK(CONVERT_TZ(created_at, '+00:00', '-03:00')) - 1 as day_of_week,
            HOUR(CONVERT_TZ(created_at, '+00:00', '-03:00')) as hour,
            COUNT(*) as count
        ")
        ->where('created_at', '>=', now()->subDays(90))
        ->groupBy('day_of_week', 'hour')
        ->orderBy('day_of_week')
        ->orderBy('hour')
        ->get()
        ->map(function ($item) {
            $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            return [
                'day_of_week' => (int) $item->day_of_week,
                'day_name'    => $days[$item->day_of_week],
                'hour'        => (int) $item->hour,
                'hour_label'  => sprintf('%02d:00', $item->hour),
                'count'       => (int) $item->count,
            ];
        });
        
    $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
    $matrix = [];

    for ($day = 0; $day < 7; $day++) {
        for ($hour = 0; $hour < 24; $hour++) {
            $found = $frequencies->first(fn($item) => $item['day_of_week'] === $day && $item['hour'] === $hour);
            $matrix[] = [
                'day_of_week' => $day,
                'day_name'    => $days[$day],
                'hour'        => $hour,
                'hour_label'  => sprintf('%02d:00', $hour),
                'count'       => $found ? $found['count'] : 0,
            ];
        }
    }

    return response()->json(['data' => $matrix]);
}
}
