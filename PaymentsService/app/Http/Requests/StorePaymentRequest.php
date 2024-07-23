<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'order' => ['required'],
            'type' => ['required'],
            'cardName' => ['required'],
            'cardNumber' => ['required'],
            'expirationDate' => ['required'],
            'cvcCvv' => ['required'],
            'money' => ['required', 'numeric', 'min:50', 'max:10000'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'order_id' => $this->order,
            'payment_type_id' => $this->type,
            'card_name' => $this->cardName,
            'card_number' => $this->cardNumber,
            'card_expiration_date' => $this->expirationDate,
            'card_code' => $this->cvcCvv,
            'money' => rand(50, 10000),
        ]);
    }
}
