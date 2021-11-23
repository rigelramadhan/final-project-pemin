<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getUserById($userId) {
        $user = User::where('id', $userId)->first();
        
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => "User has been registered.",
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]
            ], 200);
        }
    }

    public function updateUser($userId) {

    }

    public function deleteUser($userId) {

    }
}
