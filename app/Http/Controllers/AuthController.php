<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Carbon\Carbon;

class AuthController extends Controller
{
	public function login(LoginRequest $request){
		try {
	        $credentials = request(['email', 'password']);

	        if (!Auth::attempt($credentials)){
	            return response()->json([
	            	'status' => 401,
	                'message' => 'No autorizado'
	            ], 401);
	        }

	        $user = $request->user();
	        $tokenResult = $user->createToken('Personal Access Token');

	        $token = $tokenResult->token;
	        if ($request->remember_me)
	            $token->expires_at = Carbon::now()->addWeeks(1);
	        $token->save();

	        return response()->json([
				'status' => 200,
				'message' => 'Inicio de sesión exitoso',
				'data' => [
		            'access_token' => $tokenResult->accessToken,
		            'token_type' => 'Bearer',
		            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
				]
	        ]);
		} catch (Exception $e) {
			return response()->json([
				'status' => 500,
				'message' => 'No se ha iniciar sesión en este momento',
				'data' => [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]
			],500);
		}
	}

	public function logout(Request $request)
	{
		try {
			$user = $request->user();
			$request->user()->token()->revoke();
			return response()->json([
				'status' => 200,
				'message' => 'Deslogueo exitoso'
			]);
		} catch (Exception $e) {
			return response()->json([
				'status' => 500,
				'message' => 'No se ha podido hacer logout en estos momentos.',
				'data' => [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]
			],500);
		}
	}
}
