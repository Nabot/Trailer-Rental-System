<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('inquiries.create');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:rental,sales',
            'source' => 'required|in:website,phone,referral,walk_in,social_media,google_ads,other',
            'status' => 'nullable|in:new,contacted,quoted,follow_up,converted,lost,on_hold',
            'priority' => 'nullable|in:high,medium,low',
            'customer_id' => 'nullable|exists:customers,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'preferred_start_date' => 'nullable|date',
            'preferred_end_date' => 'nullable|date|after_or_equal:preferred_start_date',
            'trailer_interests' => 'nullable|array',
            'trailer_interests.*' => 'exists:trailers,id',
            'rental_purpose' => 'nullable|string|max:1000',
            'budget_range' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
            'create_anyway' => 'nullable|boolean',
        ];
    }
}
