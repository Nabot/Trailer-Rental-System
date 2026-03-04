<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
});

test('sales rep can access commission report', function () {
    $salesRep = User::factory()->create();
    $salesRep->assignRole('sales_rep');

    $response = $this->actingAs($salesRep)->get(route('reports.commission'));
    $response->assertStatus(200);
    $response->assertSee('Commission');
});

test('admin can access commission report', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('reports.commission'));
    $response->assertStatus(200);
});

test('guest cannot access commission report', function () {
    $response = $this->get(route('reports.commission'));
    $response->assertRedirect(route('login'));
});
