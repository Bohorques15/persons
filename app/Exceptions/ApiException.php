<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;

class ApiException extends Exception
{
    protected $validator;

    protected $code = 422;

    public function __construct(Validator $validator) {
        $this->validator = $validator;
    }

    public function render() {

    	$errors = [];
    	$previo_campo = null;

    	foreach($this->validator->errors()->getMessages() as $key => $validationErrors):
	        if (is_array($validationErrors)) {
	            foreach($validationErrors as $validationError):
    				$errors[] = $validationError;
	            endforeach;
	        } else {
	            $error[] = $validationErrors;
	        }
	    endforeach;

        return response()->json([
            'message' => 'Ha ocurrido un error, porfavor intente mas tarde.',
            'data' => [
                'errors' => $errors
            ]
        ], $this->code);
    }
}
