<?php

use App\Models\Enrollment;
use App\Models\Frequency;
use App\Models\Plan;
use App\Models\Student;
use App\Models\User;

function createStudentWithEnrollment(string $studentStatus = 'active', ?User $user = null): array
{
    $user ??= User::factory()->create(['points' => 0]);

    $student = Student::create([
        'user_id' => $user->id,
        'status' => $studentStatus,
    ]);

    $plan = Plan::create([
        'name' => 'Mensal',
        'price' => 99.90,
        'duration_days' => 30,
        'status' => 'active',
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'plan_id' => $plan->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
        'status' => 'active',
    ]);

    return [$user, $student, $plan, $enrollment];
}

it('registers today frequency once and gives points', function () {
    [$user, $student, , $enrollment] = createStudentWithEnrollment();

    $response = $this->actingAs($user)
        ->postJson(route('frequency.register'));

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Presença registrada com sucesso!')
        ->assertJsonPath('data.points_earned', 10)
        ->assertJsonPath('data.total_points', 10);

    expect(Frequency::where('student_id', $student->id)->count())->toBe(1)
        ->and(Frequency::first()->enrollment_id)->toBe($enrollment->id)
        ->and($user->fresh()->points)->toBe(10);
});

it('does not duplicate frequency or points on a second click in the same day', function () {
    [$user, $student] = createStudentWithEnrollment();

    $this->actingAs($user)->postJson(route('frequency.register'))->assertCreated();

    $response = $this->actingAs($user)
        ->postJson(route('frequency.register'));

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Presença já registrada hoje.')
        ->assertJsonPath('data.points_earned', 0)
        ->assertJsonPath('data.total_points', 10);

    expect(Frequency::where('student_id', $student->id)->count())->toBe(1)
        ->and($user->fresh()->points)->toBe(10);
});

it('allows a new frequency after cancelling an enrollment and creating another one', function () {
    [$user, $student, $plan, $oldEnrollment] = createStudentWithEnrollment();

    $this->actingAs($user)->postJson(route('frequency.register'))->assertCreated();

    $oldEnrollment->update([
        'status' => 'cancelled',
        'cancelled_at' => now(),
    ]);

    $newEnrollment = Enrollment::create([
        'student_id' => $student->id,
        'plan_id' => $plan->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('frequency.register'));

    $response
        ->assertCreated()
        ->assertJsonPath('data.points_earned', 10)
        ->assertJsonPath('data.total_points', 20);

    expect(Frequency::where('student_id', $student->id)->count())->toBe(2)
        ->and(Frequency::latest('id')->first()->enrollment_id)->toBe($newEnrollment->id);
});

it('blocks frequency when the student access is not active', function () {
    [$user] = createStudentWithEnrollment('delinquent');

    $response = $this->actingAs($user)
        ->postJson(route('frequency.register'));

    $response
        ->assertForbidden()
        ->assertJsonPath('message', 'Seu acesso está suspenso por inadimplência. Regularize seu pagamento.');
});

