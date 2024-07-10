<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $method = $this->method();

        if ($method == "PUT") {
            return [
                'fullName' => ['required', 'regex:/^[a-zA-Z ]+$/'],
                'email' => ['required', 'email', 'unique:users', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/'],
                'password' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*+\_-])(?=.{8,})/'],
                'money',
                'role' => ['required'],
            ];
        } else {
            return [
                'fullName' => ['sometimes', 'required', 'regex:/^[a-zA-Z ]+$/'],
                'email' => ['sometimes', 'required', 'email', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/'],
                'password' => ['sometimes', 'required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*+\_-])(?=.{8,})/'],
                'money' => ['sometimes'],
                'role' => ['sometimes', 'required'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->fullName) {
            $data['full_name'] = $this->fullName;
        }

        if ($this->role) {
            $data['role_id'] = $this->role;
        }

        $this->merge($data);
    }
}
