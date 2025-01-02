<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $credential = $request->validate([
                "email" => "required|email",
                "password" => "required"
            ]);

            $user = User::where("email", $credential['email'])->first();
            if (!$user) {
                return response()->json([
                    "status" => "error",
                    "statusCode" => 404,
                    "message" => "Akun tidak ditemukan"
                ], 404);
            }

            if (Auth::attempt($credential)) {
                $token = $user->createToken($credential['email'])->plainTextToken;

                $response =  response()->json([
                    "status" => "success",
                    "statusCode" => 200,
                    "data" => [
                        "token" => $token,
                        "userId" => $user->id,
                        "role" => $user->role
                    ]
                ], 200);

                return $response;
            }
        } catch (\Throwable $th) {

            return response()->json([
                "status" => "failed",
                "statusCode" => 400,
                "message" => $th
            ], 400);
        }
    }
    public function redirect()
    {
        return response()->json([
            "message" => "unautho"
        ], 200);
    }
}
