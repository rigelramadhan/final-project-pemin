<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        } else {
            return response()->json([
                'success' => false  ,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function updateUser(Request $request, $userId) {
        $password = Hash::make($request->input('password'));
        $data = User::where('id', $userId)->update([
            'name' => $request->input('name'), 
            'email' => $request->input('email'),
            'password' => $password
        ]);

        if ($data) {
            return response()->json([
                'success' => true,
                'message' => "User has been registered.",
                'data' => [
                    'user' => [
                        'name' => $request->input('name'), 
                        'email' => $request->input('email'),
                        'password' => $password
                    ]
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false  ,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function deleteUser($userId) {

    }
}
