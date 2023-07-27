<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentChannelValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'authorization_code' => ['nullable', 'string'],
            'bin' => ['nullable', 'string'],
            'last4' => ['nullable', 'numeric'],
            'exp_month' => ['nullable', 'numeric'],
            'exp_year' => ['nullable', 'numeric'],
            'channel' => ['nullable', 'string'],
            'card_type' => ['nullable', 'string'],
            'bank' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string'],
            'brand' => ['nullable', 'string'],
            'reusable' => ['nullable', 'string'],
            'signature' => ['nullable', 'string'],
            'account_name' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric'],
            'transaction_date' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'payment_reference_id' => ['nullable', 'string'],
            'gateway_response' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
            'channel' => ['nullable', 'string'],
            'ip_address' => ['nullable', 'string'],
            'currency' => ['nullable', 'string'],
        ];
    }
}
