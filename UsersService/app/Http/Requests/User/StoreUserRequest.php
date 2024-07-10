<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'fullName' => ['required', 'regex:/^[a-zA-Z ]+$/'],
            'email' => ['required', 'email', 'unique:users', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/'],
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*+\_-])(?=.{8,})/'],
            'role' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'full_name' => $this->fullName,
            'role_id' => $this->role,
        ]);
    }
}
