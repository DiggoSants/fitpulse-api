<?php

use App\Models\Enrollment;
use App\Models\Manager;
use App\Models\Plan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;

afterEach(fn () => Carbon::setTestNow());

function createReceptionTrialManager(): User
{
    $user = User::factory()->create();

    Manager::create([
        'user_id' => $user->id,
    ]);

    return $user;
}

function createReceptionTrialStudent(): Student
{
    $user = User::factory()->create();

    return Student::create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);
}

it('returns only backend allowed trial durations for reception enrollment', function () {
    Carbon::setTestNow('2026-06-14');

    $manager = createReceptionTrialManager();

    Plan::create([
        'name' => 'Mensal',
        'price' => 99.90,
        'duration_days' => 30,
        'status' => 'active',
        'is_trial' => false,
    ]);

    Plan::create([
        'name' => 'Teste 7 dias',
        'price' => 0,
        'duration_days' => 7,
        'status' => 'active',
        'is_trial' => true,
        'trial_days' => 7,
    ]);

    Plan::create([
        'name' => 'Teste inativo',
        'price' => 0,
        'duration_days' => 14,
        'status' => 'inactive',
        'is_trial' => true,
        'trial_days' => 14,
    ]);

    $response = $this->actingAs($manager)->getJson(route('reception.plans'));

    $response
        ->assertOk()
        ->assertJsonPath('trial_available', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonCount(1, 'trial_options')
        ->assertJsonPath('trial_options.0.trial_days', 7)
        ->assertJsonPath('trial_options.0.start_date_formatted', '14/06/2026')
        ->assertJsonPath('trial_options.0.end_date_formatted', '21/06/2026');

    Carbon::setTestNow();
});

it('applies a free trial using an allowed duration', function () {
    Carbon::setTestNow('2026-06-14');

    $manager = createReceptionTrialManager();
    $student = createReceptionTrialStudent();
    $trialPlan = Plan::create([
        'name' => 'Teste 5 dias',
        'price' => 0,
        'duration_days' => 5,
        'status' => 'active',
        'is_trial' => true,
        'trial_days' => 5,
    ]);

    $response = $this->actingAs($manager)->postJson(route('reception.enroll.trial'), [
        'student_id' => $student->id,
        'trial_plan_id' => $trialPlan->id,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.trial', true)
        ->assertJsonPath('data.trial_days', 5)
        ->assertJsonPath('data.start_date', '14/06/2026')
        ->assertJsonPath('data.end_date', '19/06/2026');

    expect(Enrollment::where('student_id', $student->id)
        ->where('plan_id', $trialPlan->id)
        ->whereDate('start_date', '2026-06-14')
        ->whereDate('end_date', '2026-06-19')
        ->exists())->toBeTrue();

    Carbon::setTestNow();
});

it('rejects a free trial duration that is not allowed by backend rules', function () {
    $manager = createReceptionTrialManager();
    $student = createReceptionTrialStudent();
    $paidPlan = Plan::create([
        'name' => 'Mensal',
        'price' => 99.90,
        'duration_days' => 30,
        'status' => 'active',
        'is_trial' => false,
    ]);

    $response = $this->actingAs($manager)->postJson(route('reception.enroll.trial'), [
        'student_id' => $student->id,
        'trial_plan_id' => $paidPlan->id,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Esta duracao de teste gratis nao esta disponivel.');

    expect(Enrollment::where('student_id', $student->id)->exists())->toBeFalse();
});
