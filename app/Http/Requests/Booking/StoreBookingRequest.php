<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bookings.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trailer_id' => ['required', 'exists:trailers,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'straps_fee' => ['nullable', 'numeric', 'min:0'],
            'damage_waiver_fee' => ['nullable', 'numeric', 'min:0'],
            'required_deposit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', Rule::in(['draft', 'pending'])],
        ];
    }

    public function messages(): array
    {
        return [
            'trailer_id.required' => 'Please select a trailer.',
            'trailer_id.exists' => 'Selected trailer does not exist.',
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.after' => 'End date must be after start date.',
        ];
    }
}
