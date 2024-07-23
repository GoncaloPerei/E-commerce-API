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
        $rules = [
            'firstName' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'lastName' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'address' => ['required',  'string', 'regex:/^(Rua|Avenida|Largo|Praça|Travessa|Estrada|Alameda|Beco)\s[\w\s]+?\s\d+$/'],
            'city' => ['required', 'string', 'regex:/^[a-zA-Z,\s]+$/'],
            'postalCode' => ['required', 'regex:/^\d{4}-\d{3}$/'],
            'nif' => ['nullable', 'integer', 'regex:/^\d{9}$/'],
            'aditionalComments' => ['nullable', 'string'],
            'method',
        ];

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'postal_code' => $this->postalCode,
            'aditional_comments' => $this->aditionalComments,
        ]);
    }
}
