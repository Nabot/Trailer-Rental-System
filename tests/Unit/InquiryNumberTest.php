<?php

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->user = User::factory()->create();
});

test('generateInquiryNumber returns INQ-year-0001 when no inquiries exist', function () {
    $number = Inquiry::generateInquiryNumber();
    expect($number)->toMatch('/^INQ-\d{4}-0001$/');
});

test('generateInquiryNumber increments after an inquiry exists', function () {
    Inquiry::create([
        'inquiry_number' => 'INQ-' . now()->year . '-0001',
        'source' => 'phone',
        'name' => 'Test',
        'created_by' => $this->user->id,
        'assigned_to' => $this->user->id,
        'status' => 'new',
        'priority' => 'medium',
    ]);

    $number = Inquiry::generateInquiryNumber();
    expect($number)->toMatch('/^INQ-\d{4}-0002$/');
});

test('generateInquiryNumber does not reuse number from soft-deleted inquiry', function () {
    $inquiry = Inquiry::create([
        'inquiry_number' => 'INQ-' . now()->year . '-0001',
        'source' => 'phone',
        'name' => 'Test',
        'created_by' => $this->user->id,
        'assigned_to' => $this->user->id,
        'status' => 'new',
        'priority' => 'medium',
    ]);
    $inquiry->delete(); // soft delete

    $number = Inquiry::generateInquiryNumber();
    expect($number)->toMatch('/^INQ-\d{4}-0002$/');
});
