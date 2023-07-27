<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['nullable', 'string'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'phone_number' => 'required|numeric|unique:users,phone_number,' . $this->user->id,
            'dob' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'tell' => ['nullable', 'string'],
            'status' => ['nullable'],
            'confirmation_code' => ['nullable', 'string'],
             'confirmed' => ['nullable'],
            'date' => ['nullable', 'string'],
            'wallet' =>  ['nullable'],
            'role' =>  ['nullable', 'string'],
            'type' => ['nullable', 'string'],
           'wwallet' => ['nullable'],
          'bwallet' => ['nullable'],
            'bank' => ['nullable'],
            'bname' => ['nullable', 'string'],
            'accno' => ['nullable', 'string'],
           'accname' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'pix' => ['nullable', 'string'],
            'lga' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'ref'  => ['nullable'],
            'ccommission'  => ['nullable'],
            'pcommission'  => ['nullable'],
            'auser'  => ['nullable'],
            'is_robot' => ['nullable'],
            'games' => ['nullable', 'string'],
           'num_pos' => ['nullable', 'string'],
           'num_pos2' => ['nullable', 'string'],
            'num_pos1' => ['nullable', 'string'],
            'num_pos3' => ['nullable', 'string'],
            "ussd_action" => ['nullable'],
           'site_time' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'gl_bwallet' => ['nullable'],
           'sl_bwallet' => ['nullable'],
           'gh_bwallet' => ['nullable'],
            'lm_bwallet' => ['nullable'],
            'we_bwallet' => ['nullable'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'message' => 'Invalid data sent',
            'details' => $errors->messages(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
