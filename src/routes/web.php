<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\FrequencyController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\StudentScheduleController;
use App\Http\Controllers\WorkoutSessionController;
use App\Http\Controllers\InstructorAttendanceController;
use App\Http\Controllers\FidelityController;
use App\Http\Controllers\InstructorAvailabilityController;
use App\Http\Controllers\InstructorChangeController;
use App\Http\Controllers\EquipmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Matrícula ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/enrollment', [EnrollmentController::class, 'index'])->name('enrollment.index');
    Route::post('/enrollment', [EnrollmentController::class, 'store'])->name('enrollment.store');
    Route::post('/enrollments/{id}/cancel', [EnrollmentController::class, 'cancel'])->name('enrollment.cancel');
});

// ── Perfil ────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Alunos ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
});

// ── Exercícios ─────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled', 'role:student,manager,instructor'])->group(function () {
    Route::get('/exercises', [ExerciseController::class, 'index'])->name('exercises.index');
});

Route::middleware(['auth', 'verified', 'role:manager,instructor'])->group(function () {
    Route::resource('exercises', ExerciseController::class)->except(['index', 'show']);
    Route::get('/exercise-images', [ExerciseController::class, 'searchImages'])->name('exercise.images');
});

// ── Treinos ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled', 'role:student,manager,instructor'])->group(function () {
    Route::resource('workouts', WorkoutController::class)->only([
        'create',
        'store',
        'edit',
        'update',
        'destroy',
        'index',
        'show'
    ]);
});

// ── Instrutores ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::resource('instructors', InstructorController::class);
});

Route::middleware(['auth', 'verified', 'role:manager,instructor'])->group(function () {
    Route::post('/instructors/{id}/regenerate-code', [InstructorController::class, 'regenerateCode'])
        ->name('instructors.regenerate-code');
});

// ── Relatório de fidelidade (gerentes e instrutores) ─────────────────────────
Route::middleware(['auth', 'verified', 'role:manager,instructor'])->group(function () {
    Route::get('/reports/plans/loyalty', [ReportController::class, 'plansLoyalty'])->name('reports.plans.loyalty');
});

// ── Relatórios (só gerentes) ──────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/reports/plans/comparative',   [ReportController::class, 'plansComparative'])->name('reports.plans.comparative');
    Route::get('/reports/plans/cancellations', [ReportController::class, 'plansCancellations'])->name('reports.plans.cancellations');
    Route::get('/reports/users/delinquency',   [ReportController::class, 'usersDelinquency'])->name('reports.users.delinquency');
    Route::get('/reports/plans/occupation',    [ReportController::class, 'plansOccupation'])->name('reports.plans.occupation');
    Route::get('/reports/frequency/heatmap',   [FrequencyController::class, 'heatmap'])->name('reports.frequency.heatmap');
    Route::get('/reports/frequency',           function () {
        return view('reports.frequency-heatmap');
    })->name('reports.frequency.view');
    Route::get('/reports/shop/products',       [ShopController::class, 'report'])->name('reports.shop.products');
});

// ── Renovação de planos (ANTES do resource para evitar conflito de rota) ──────
Route::middleware(['auth', 'verified', 'enrolled', 'role:student'])->group(function () {
    Route::get('/plans/renewals', [RenewalController::class, 'history'])->name('plans.renewals');
    Route::post('/plans/renew',   [RenewalController::class, 'renew'])->name('plans.renew');
});

// ── Planos (só gerentes) ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::resource('plans', PlanController::class);
    Route::post('/plans/{id}/restore', [PlanController::class, 'restore'])->name('plans.restore');
});

// ── Mensalidade ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled', 'role:student'])->group(function () {
    Route::get('/billing',          [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/process', [BillingController::class, 'process'])->name('billing.process');
});

Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/billing/all', [BillingController::class, 'all'])->name('billing.all');
});

// ── Controle de acesso ────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/access', function () {
        return view('access.index');
    })->name('access.index');
});

Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/access/students',  [AccessController::class, 'students'])->name('access.students');
    Route::post('/access/block',    [AccessController::class, 'block'])->name('access.block');
    Route::post('/access/unblock',  [AccessController::class, 'unblock'])->name('access.unblock');
    Route::post('/access/status',   [AccessController::class, 'updateStatus'])->name('access.status');
});

// ── Frequência ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled', 'role:student'])->group(function () {
    Route::post('/frequency/register', [FrequencyController::class, 'register'])->name('frequency.register');
});

Route::middleware(['auth', 'verified', 'role:instructor'])->group(function () {
    Route::get('/instructor/frequency', [FrequencyController::class, 'instructorStudents'])->name('instructor.frequency.students');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/exercise-video', function (\Illuminate\Http\Request $request) {
        $query = urlencode($request->q . ' exercício como executar corretamente');
        $key   = env('YOUTUBE_API_KEY');

        if (!$key || trim((string) $request->q) === '') {
            return response()->json(['video_id' => null]);
        }

        // videoDuration=short = vídeos de até 4 minutos
        // order=relevance = mais relevante primeiro
        $url = "https://www.googleapis.com/youtube/v3/search"
            . "?part=snippet"
            . "&q={$query}"
            . "&type=video"
            . "&maxResults=1"
            . "&relevanceLanguage=pt"
            . "&videoDuration=short"
            . "&order=relevance"
            . "&key={$key}";

        $rawData = @file_get_contents($url);
        $data    = $rawData ? json_decode($rawData, true) : [];
        $videoId = $data['items'][0]['id']['videoId'] ?? null;
        return response()->json(['video_id' => $videoId]);
    })->name('exercise.video');
});

// ── Lojinha ───────────────────────────────────────────────────────────────────
// Listagem — todos autenticados podem ver
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products', [ShopController::class, 'index'])->name('products.index');
});

// Compra — alunos matriculados e gerentes
Route::middleware(['auth', 'verified', 'enrolled', 'role:student'])->group(function () {
    Route::post('/sales', [ShopController::class, 'sale'])->name('sales.store');
    Route::get('/lojinha', [ShopController::class, 'studentView'])->name('shop.index');
});

// Cadastro e gerenciamento — só gerentes
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    // CRUD básico (já existentes)
    Route::post('/products',              [ShopController::class, 'store'])->name('products.store');
    Route::put('/products/{id}',          [ShopController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}',       [ShopController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/restore', [ShopController::class, 'restore'])->name('products.restore');
    Route::get('/lojinha/manager',        [ShopController::class, 'managerView'])->name('shop.manager');

    // ========== NOVAS ROTAS PARA CONTROLE DE ESTOQUE ==========
    // Visualizar estoque de todos os produtos
    Route::get('/manager/products/stock', [ShopController::class, 'managerStock'])->name('products.stock');
    // Atualizar estoque de um produto
    Route::put('/manager/products/{id}/stock', [ShopController::class, 'updateStock'])->name('products.update-stock');
    // Reposição de estoque (incremento)
    Route::post('/manager/products/{id}/restock', [ShopController::class, 'restock'])->name('products.restock');
    // Produtos com estoque baixo (alerta)
    Route::get('/manager/products/low-stock', [ShopController::class, 'lowStock'])->name('products.low-stock');
});

// ── Avaliação física ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:student,manager,instructor'])->group(function () {
    Route::post('/evaluations',                          [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{user_id}',                 [EvaluationController::class, 'history'])->name('evaluations.history');
    Route::get('/reports/physical/evolution/{user_id}',  [EvaluationController::class, 'evolution'])->name('reports.physical.evolution');
});

// ── Evolução Física — views ───────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled', 'role:student'])->group(function () {
    Route::get('/evolucao', [EvaluationController::class, 'studentPage'])->name('evaluations.page');
});

Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/evolucao/gerente', [EvaluationController::class, 'managerPage'])->name('evaluations.manager');
});

Route::middleware(['auth', 'verified', 'role:manager,instructor'])->group(function () {
    Route::get('/evolucao/instrutor', [EvaluationController::class, 'instructorPage'])->name('evaluations.instructor');
});

// ── Manutenção de equipamentos ────────────────────────────────────────────────
// Listagem — todos autenticados podem ver
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/maintenance', [MaintenanceController::class, 'view'])->name('maintenance.view');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api/equipment',   [MaintenanceController::class, 'equipment'])->name('equipment.index');
    Route::get('/api/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
});

// Registro e resolução — só gerentes
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::post('/api/equipment',       [MaintenanceController::class, 'storeEquipment'])->name('equipment.store');
    Route::post('/api/maintenance',     [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::put('/api/maintenance/{id}', [MaintenanceController::class, 'resolve'])->name('maintenance.resolve');
});

// ── Gamificação e planos conjuntos ────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled', 'role:student'])->group(function () {
    Route::get('/gamification',             [GamificationController::class, 'index'])->name('gamification.index');
    Route::get('/plan-groups',              [GamificationController::class, 'listGroups'])->name('plan-groups.index');
    Route::post('/plan-groups',             [GamificationController::class, 'createGroup'])->name('plan-groups.store');
    Route::post('/plan-groups/{id}/join',   [GamificationController::class, 'joinGroup'])->name('plan-groups.join');
    Route::post('/plan-groups/{id}/leave',  [GamificationController::class, 'leaveGroup'])->name('plan-groups.leave');
});

// ── Recepção ──────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified', 'role:manager,receptionist'])->group(function () {
    Route::get('/students/pending-enrollment', [ReceptionController::class, 'pendingEnrollment'])->name('reception.pending');
    Route::get('/api/students/pending-enrollment', [ReceptionController::class, 'pendingEnrollmentData'])->name('reception.pending.data');
    Route::get('/reception/instructors/available', [ReceptionController::class, 'availableInstructors'])->name('reception.instructors');
    Route::get('/reception/plans',             [ReceptionController::class, 'activePlans'])->name('reception.plans');
    Route::post('/enrollments',                [ReceptionController::class, 'enroll'])->name('reception.enroll');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/student-schedule', [StudentScheduleController::class, 'store'])->name('student-schedule.store');
    Route::get('/student-schedule/{userId?}', [StudentScheduleController::class, 'show'])->name('student-schedule.show');
    Route::get('/student-schedule/validate/{userId}', [StudentScheduleController::class, 'validateSchedule']);
});

Route::middleware(['auth'])->group(function () {
    // Grupos musculares disponíveis (vem da coluna muscle_group)
    Route::get('/muscle-groups', [WorkoutController::class, 'getMuscleGroups']);

    // Filtrar exercícios por grupos musculares
    Route::post('/exercises/filter', [WorkoutController::class, 'filterExercisesByMuscleGroup']);

    // Buscar exercícios por grupos musculares
    Route::post('/exercises/by-muscle-groups', [WorkoutController::class, 'getExercisesByMuscleGroups']);

    Route::get('/workouts/student/{studentId}', [WorkoutController::class, 'getStudentWorkouts']);
});

// Rotas para execução de treino (aluno)
Route::middleware(['auth'])->prefix('treino')->group(function () {
    Route::get('/hoje', fn () => redirect()->route('workouts.index'))->name('workout-sessions.today');
    Route::post('/sessao/{sessionId}/iniciar', [WorkoutSessionController::class, 'start'])->name('workout-sessions.start');
    Route::post('/sessao/{sessionId}/finalizar', [WorkoutSessionController::class, 'complete'])->name('workout-sessions.complete');
    Route::post('/exercicio/{sessionExerciseId}/completar', [WorkoutSessionController::class, 'completeExercise'])->name('workout-sessions.complete-exercise');
    Route::get('/exercicio/{sessionExerciseId}/detalhes', [WorkoutSessionController::class, 'getExerciseDetails'])->name('workout-sessions.exercise-details');
    Route::get('/historico', [WorkoutSessionController::class, 'history'])->name('workout-sessions.history');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Frequência - Instrutor
    Route::get('/instructor/attendances', [InstructorAttendanceController::class, 'index']);
    Route::get('/instructor/attendances/absent', [InstructorAttendanceController::class, 'absentStudents']);
    Route::get('/instructor/attendances/student/{studentId}', [InstructorAttendanceController::class, 'show']);
    Route::post('/instructor/attendances/student/{studentId}/mark', [InstructorAttendanceController::class, 'markAttendance']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/fidelity', [FidelityController::class, 'show']); // para o próprio aluno
    Route::get('/fidelity/student/{studentId}', [FidelityController::class, 'showForInstructor']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/instructor/students', [InstructorController::class, 'myStudents'])->name('instructor.students');
});
Route::middleware(['auth'])->post('/enrollment/trial', [EnrollmentController::class, 'trial'])->name('enrollment.trial');
// Instrutor gerencia sua própria agenda
Route::middleware(['auth'])->group(function () {
    Route::get('/instructor/availability', [InstructorAvailabilityController::class, 'index'])->name('instructor.availability');
    Route::post('/instructor/availability', [InstructorAvailabilityController::class, 'store'])->name('instructor.availability.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/api/instructors/available', [InstructorAvailabilityController::class, 'availableInstructors']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/instructors/available', [InstructorChangeController::class, 'availableInstructors'])
        ->name('instructors.available');
    Route::post('/instructor/change', [InstructorChangeController::class, 'change'])
        ->name('instructor.change');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('equipment', EquipmentController::class);
    Route::get('equipment/active/list', [EquipmentController::class, 'active'])->name('equipment.active');
});
require __DIR__ . '/auth.php';
