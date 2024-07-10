<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductCategoryRequest extends FormRequest
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
                'name' => ['required', 'unique:product_categories', 'regex:/^[a-zA-Z ]+$/'],
                'image' => ['unique:product_categories'],
                'status' => ['sometimes', 'required'],
            ];
        } else {
            return [
                'name' => ['sometimes', 'required', 'unique:product_categories', 'regex:/^[a-zA-Z ]+$/'],
                'image' => ['sometimes', 'unique:product_categories'],
                'status' => ['sometimes', 'required'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->status) {
            $data['product_status_id'] = $this->status;
        }

        $this->merge($data);
    }
}
