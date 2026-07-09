<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:import,export',
            'commodity' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'container_type' => 'required|string|max:100',
            'origin_country' => 'required|string|max:255',
            'origin_port' => 'required|string|max:255',
            'destination_country' => 'required|string|max:255',
            'destination_port' => 'required|string|max:255',
        ];
    }
}
