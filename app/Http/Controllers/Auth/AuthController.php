<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->getMessageBag(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            $accessToken = $user->createToken('authToken')->accessToken;
            $user['access_token'] = $accessToken;

            return $this->successResponse($user);
        }

        return $this->errorResponse('error on registration', 500);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->getMessageBag(), 422);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            $user = User::find(auth()->user()->id);
            $user['access_token'] = $user->createToken('authToken')->accessToken;

            return $this->successResponse($user);
        }
        return $this->errorResponse('Les informations d\'identification sont invalides', 422);
    }

    public function getAuthUser()
    {
        return User::find(auth()->user()->id);
    }

    public function profile()
    {
        return $this->successResponse($this->getAuthUser());
    }
}
