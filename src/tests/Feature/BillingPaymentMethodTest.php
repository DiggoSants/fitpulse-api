<?php

use App\Models\Billing;
use App\Models\Enrollment;
use App\Models\Plan;
use App\Models\Student;
use App\Models\User;

function createBillableStudent(): array
{
    $user = User::factory()->create(['points' => 0]);

    $student = Student::create([
        'user_id' => $user->id,
        'status' => 'active',
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

it('rejects boleto for new payment processing', function () {
    [$user] = createBillableStudent();

    $response = $this->actingAs($user)
        ->postJson(route('billing.process'), [
            'payment_method' => 'boleto',
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors('payment_method');
});

it('reprocesses an old pending boleto with pix', function () {
    [$user, $student, $plan, $enrollment] = createBillableStudent();

    $billing = Billing::create([
        'student_id' => $student->id,
        'plan_id' => $plan->id,
        'enrollment_id' => $enrollment->id,
        'payment_method' => 'boleto',
        'amount' => $plan->price,
        'status' => 'pending',
        'paid_at' => null,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('billing.process'), [
            'payment_method' => 'pix',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.payment_method', 'pix')
        ->assertJsonPath('data.status', 'confirmed');

    $billing->refresh();

    expect($billing->payment_method)->toBe('pix')
        ->and($billing->status)->toBe('confirmed')
        ->and($billing->paid_at)->not->toBeNull();
});

