<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public form, no auth required
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trailer_id' => ['required', 'exists:trailers,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'id_number' => ['required', 'string', 'max:50'],
            'driver_licence' => ['required', 'string', 'max:50'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'straps_fee' => ['nullable', 'numeric', 'min:0'],
            'damage_waiver_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
