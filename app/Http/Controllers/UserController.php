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

    public function getUsers() {
        $users = User::all();

        if ($users) {
            return response()->json([
                'succes' => true,
                'message'=> "All Users available",
                'data' => [
                    'users' => $users
                ]
            ], 200);
        } else{
            return response()->json([
                'succes' => false,
                'message'=> "User not available"
            ], 200);
        }
    }

    public function getUserById(Request $request, $userId) {
        $user = User::where('id', $userId)->first();

        if ($user) {
            if ($request->user->role == 'admin' || $request->user->email == $user->email) {
                return response()->json([
                    'success' => true,
                    'message' => "User found.",
                    'data' => [
                        'user' => $user
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false  ,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
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

        $user = User::where('id', $userId)->first();

        if ($data) {
            return response()->json([
                'success' => true,
                'message' => "User has been updated.",
                'data' => [
                    'user' => [
                        'name' => $user->name, 
                        'email' => $user->email,
                        'password' => $user->password
                    ]
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function deleteUser($userId) {
        $user = User::find($userId);

        if ($user->role == 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User has been deleted'
        ], 200);
    }


}
