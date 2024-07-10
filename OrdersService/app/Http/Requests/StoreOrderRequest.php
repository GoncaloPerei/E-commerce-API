<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstName' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'lastName' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'address' => ['required'],
            'city' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'country' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'postalCode' => ['required', 'regex:/^\d{4}-\d{3}$/'],
            'nif' => ['regex:/^\d{9}$/'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'postal_code' => $this->postalCode,
        ]);
    }
}
