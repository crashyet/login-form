<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    function register(Request $request) {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'in:admin,user',
            ]);

            // $email_is_taken = User::where('email', $request->email)->first();
            // if ($email_is_taken) {
            //     return Response()->json([
            //         'message' => 'Email already taken',
            //     ], 409);
            // }

            // Create a new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->input('role', 'user'),
            ]);

            return Response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'validasi gagal',
                'error' => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    function login(Request $request) {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->password == $request->password) {
            return Response()->json([
                'message' => 'Login success',
                'user' => $user,
            ], 200);
        } else {
            return Response()->json([
                'message' => 'Login failed',
            ], 401);
        }
    }
}