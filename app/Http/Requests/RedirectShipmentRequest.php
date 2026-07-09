<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedirectShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_country' => 'required|string|max:255',
            'destination_port' => 'required|string|max:255',
            'reason' => 'required|string',
            'estimated_arrival' => 'required|date',
            'shipping_cost' => 'required|numeric',
            'estimated_profit' => 'required|numeric',
        ];
    }
}
