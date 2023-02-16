<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if ($request->ajax()) {
            return response()->json([
                'status' => 422,
                'message' => "Ha ocurrido un error",
                'data' => [
                    'errors' => $errors
                ]
            ]); 
        }

        if ($e->response) {
            return $e->response;
        }

        return $request->expectsJson()
                    ? $this->invalidJson($request, $e)
                    : $this->invalid($request, $e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($request->wantsJson()){

            if ($exception instanceof ValidationException){
                return $this->convertValidationExceptionToResponse($exception, $request);
            }

            if ($exception instanceof ModelNotFoundException){
                $model = strtolower( \class_basename( $exception->getModel() ) ); 

                return response()->json([
                    'status' => 404,
                    'message' => "No existe niguna instancia de {$model} con el id especificado.",
                    'data' => []
                ], 404);   
            }

            if ($exception instanceof AuthenticationException){
                return response()->json([
                    'status' => 401,
                    'message' => "No autenticado.",
                    'data' => []
                ], 401);  
            }

            if ($exception instanceof AuthorizationException){
                return response()->json([
                    'status' => 403,
                    'message' => "No autorizado.",
                    'data' => [
                        'error' => $exception->getMessage()
                    ]
                ], 403);
            }

            if ($exception instanceof NotFoundHttpException){
                return response()->json([
                    'status' => 404,
                    'message' => "No se encontro la URL especificada.",
                    'data' => []
                ], 404);   
            }

            if ($exception instanceof MethodNotAllowedHttpException){
                return response()->json([
                    'status' => 403,
                    'message' => "No autorizado",
                    'data' => [
                        'error' => $exception->getMessage()
                    ]
                ], 403);   
            }

            if ($exception instanceof HttpException){
                return response()->json([
                    'status' => $exception->getStatusCode(),
                    'message' => $exception->getMessage(),
                    'data' => []
                ], $exception->getStatusCode());    
            }

            if ($exception instanceof QueryException){
                // $codeQuery = $exception->errorInfo[1];
                return response()->json([
                    'status' => 409,
                    'message' => "Ha ocurrido un error al tratar de realizar la operación en la BD.",
                    'data' => [
                        'error' => $exception->getMessage()
                    ]
                ], 409);  
            }

            return response()->json([
                'status' => 500,
                'message' => "Falla inesperada.",
                'data' => [
                    'error' => $exception->getMessage()
                ]
            ], 500); 
        }

        return parent::render($request, $exception);
    }
}
