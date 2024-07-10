<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'title' => ['sometimes', 'required|unique:products'],
            'description' => ['sometimes', 'max:255'],
            'price' => ['sometimes', 'required', 'regex:/^\d+\.\d+$/'],
            'image' => ['sometimes', 'required', 'regex:/\b((https?|ftp):\/\/[-\w+&@#\/%?=~_|!:,.;]*[-\w+&@#\/%=~_|])/'],
            'stock' => ['sometimes', 'required', 'regex:/^\d+$/'],
            'category' => ['sometimes', 'required'],
            'status' => ['sometimes', 'required'],
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->status) {
            $data['product_status_id'] = $this->status;
        }

        if ($this->category) {
            $data['product_category_id'] = $this->category;
        }

        $this->merge($data);
    }
}
