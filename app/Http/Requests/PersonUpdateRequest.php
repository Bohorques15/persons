<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\ApiException;

class PersonUpdateRequest extends FormRequest
{
    public function messages()
    {
        $messages = [
            'id.required' => 'Indique el id de la persona.',
            'id.exists' => 'La persona no existe en nuestra base de datos.',
            'first_name.required' => 'El nombre es requerido',
            'first_name.string' => 'El nombre debe ser de tipo String',
            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser de tipo String',
            'document.required' => 'El document es requerido',
            'document.string' => 'El document debe ser de tipo String',
            'ima_profile.required' => 'El ima profile es requerido',
            'ima_profile.string' => 'El ima profile debe ser de tipo String',
            'ima_profile.unique' => 'El ima profile debe ser unico'
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
            'id' => 'required|exists:persons,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'document' => 'required|string',
            'ima_profile' => 'required|string|unique:persons'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new ApiException($validator);
    }
}
