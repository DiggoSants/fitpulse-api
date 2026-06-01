<?php

use App\Models\Manager;
use App\Models\Plan;
use App\Models\User;

function createPlanManager(): User
{
    $user = User::factory()->create();

    Manager::create([
        'user_id' => $user->id,
    ]);

    return $user;
}

it('lists manager dashboard plans by price', function () {
    $manager = createPlanManager();

    Plan::create([
        'name' => 'Premium',
        'price' => 149.90,
        'duration_days' => 30,
        'status' => 'active',
    ]);

    Plan::create([
        'name' => 'Basico',
        'price' => 85.00,
        'duration_days' => 30,
        'status' => 'active',
    ]);

    Plan::create([
        'name' => 'Black',
        'price' => 99.99,
        'duration_days' => 30,
        'status' => 'active',
    ]);

    $response = $this->actingAs($manager)->get(route('dashboard'));

    $response->assertOk();

    $plans = $response->viewData('plans');

    expect($plans->pluck('name')->all())->toBe([
        'Basico',
        'Black',
        'Premium',
    ]);
});

it('validates required fields when creating a plan from admin', function () {
    $manager = createPlanManager();

    $response = $this->actingAs($manager)
        ->from(route('plans.create'))
        ->post(route('plans.store'), [
            'name' => '',
            'price' => '',
            'duration_days' => '',
        ]);

    $response
        ->assertRedirect(route('plans.create'))
        ->assertSessionHasErrors([
            'name',
            'price',
            'duration_days',
        ]);

    expect(Plan::count())->toBe(0);
});
