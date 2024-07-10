<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'title' => ['required', 'unique:products'],
            'description' => ['max:255'],
            'price' => ['required', 'regex:/^\d+\.\d+$/'],
            'image' => ['required', 'unique:products'],
            'stock' => ['required', 'regex:/^\d+$/'],
            'category' => ['required'],
            'status' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_category_id' => $this->category,
            'product_status_id' => $this->status,
        ]);
    }
}
