<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'     => 'required|unique:users|email|max:255',
            'cpf'       => [
                'required',
                'unique:users',
                'max:14',
                function($attribute, $value, $fail) {
                    $cpf = preg_replace( '/[^0-9]/is', '', $value );

                    if (strlen($cpf) != 11 || !preg_match('/(\d){11}/', $cpf)) {
                        $fail("The attributte {$attribute} is invalid");
                        return;
                    }
                }
            ],
            'name'      => 'required|max:255',
            'is_admin'  => 'required|boolean'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, $this->response([
            'Error' => $validator->errors()
        ]));
    }

    public function response(array $errors)
    {
        return response($errors, Response::HTTP_BAD_REQUEST);
    }
}
