<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\ApiException;

class LoginRequest extends FormRequest
{
    public function messages()
    {
        $messages = [
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser de tipo String',
            'email.required' => 'El email es requerido',
            'email.email' => 'El formato del email es incorrecto',
            'email.string' => 'El email del empleado debe ser de tipo String'
        ];
        return $messages;
    }
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
            'password' => 'required|string',
            'email'    => 'required|string|email'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new ApiException($validator);
    }
}
