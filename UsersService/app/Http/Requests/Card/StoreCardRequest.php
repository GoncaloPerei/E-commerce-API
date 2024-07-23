<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
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
            'cardNumber' => ['required', 'numeric', 'digits_between:13,16', 'unique:cards,card_number'],
            'expirationDate' => ['required', 'date_format:m/y', 'after_or_equal:' . date('m/y')],
            'cvv' => ['required', 'numeric', 'digits:3'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'card_number' => $this->cardNumber,
            'card_expiration_date' => $this->expirationDate,
            'card_code' => $this->cvv,
        ]);
    }
}
