<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
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

    public function createTransaction(Request $request){
        // belum lengkap
        $data = Transaction::create($request->all());
        return response()->json([
            'success'=>true,
            'message'=>'transaction created',
            'data' => [
                'Transaction' => [
                        'id'=>$data->id,
                        'user' => [
                            'name' => $data->name,
                            'email' => $data->email
                        ],'book' => [
                            'title'=>$data->title,
                            'author'=>$data->author,
                        ],'deadline'=>$data->deadline,
                        'created_at'=>$data->created_at,
                        'update_at'=>$data->update_at
                ],
            ],
        ],200);
    }

    public function getTransaction($userId) {
        // belum lengkap
        $user = User::find($userId);

        if($user->role == 'admin'){
            $data = Transaction::all();
            return response()->json([
                'success' => true,
                'message' => 'All Transaction',
                'data' => [
                    'Transaction' => [
                            'id'=>$data->id,
                            'user' => [
                                'name' => $data->name,
                                'email' => $data->email
                            ],'book' => [
                                'title'=>$data->title,
                                'author'=>$data->author,
                            ],'deadline'=>$data->deadline,
                            'created_at'=>$data->created_at,
                            'update_at'=>$data->update_at
                    ],
                ],
            ], 200);
        }
        if(!$user){
            $data = Transaction::find($user->Id);
            return response()->json([
                'success' => true,
                'message' => 'All Transaction',
                'data' => [
                    'Transaction' => [
                            'id'=>$data->id,
                            'user' => [
                                'name' => $data->name,
                                'email' => $data->email
                            ],'book' => [
                                'title'=>$data->title,
                                'author'=>$data->author,
                            ],'deadline'=>$data->deadline,
                            'created_at'=>$data->created_at,
                            'update_at'=>$data->update_at
                    ],
                ],
            ], 200);
        }
    }
    // TODO: Create transaction logic
}
