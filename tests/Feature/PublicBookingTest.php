<?php

use App\Models\Trailer;

test('public booking form is accessible when trailer and dates provided', function () {
    $trailer = Trailer::factory()->available()->create();
    $start = now()->addDays(2)->format('Y-m-d');
    $end = now()->addDays(4)->format('Y-m-d');

    $response = $this->get(route('book.form', [
        'trailer_id' => $trailer->id,
        'start_date' => $start,
        'end_date' => $end,
    ]));
    $response->assertStatus(200);
});

test('public booking store creates booking and redirects to confirmation', function () {
    $trailer = Trailer::factory()->available()->create([
        'rate_per_day' => 550,
    ]);

    $start = now()->addDays(2)->format('Y-m-d');
    $end = now()->addDays(4)->format('Y-m-d');

    $response = $this->post(route('book.store'), [
        '_token' => csrf_token(),
        'trailer_id' => $trailer->id,
        'start_date' => $start,
        'end_date' => $end,
        'name' => 'Test Customer',
        'phone' => '0812345678',
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
    $booking = \App\Models\Booking::where('customer_id', '>', 0)->latest()->first();
    expect($booking)->not->toBeNull();
    expect($booking->booking_number)->toStartWith('TRL-');
});
