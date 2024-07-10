<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
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
            'name' => ['required', 'unique:product_categories', 'regex:/^[a-zA-Z ]+$/'],
            'image' => ['unique:product_categories', 'regex:/\b((https?|ftp):\/\/[-\w+&@#\/%?=~_|!:,.;]*[-\w+&@#\/%=~_|])/'],
            'status' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_status_id' => $this->status,
        ]);
    }
}
