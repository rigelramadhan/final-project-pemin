<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $deadline = Carbon::now()->addWeek()->toDateString();
        $validation = Validator::make($request->all(), [
            'book_id' => 'required',
            // 'user_id' => 'required',
            // 'deadline' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fields cannot be empty.'
            ], 400);
        }
        $book = Book::find($request->book_id);
        if(!$book){
            return response()->json([
                'success' => false,
                'message' => 'Book not found',
            ], 400);
        };
        
        $data = [
            'book_id' => $request->book_id,
            'user_id' => $request->user->id,
            'deadline' => $deadline
        ];
        
        $transaction = Transaction::create($data);

        return response()->json([
            'success'=>true,
            'message'=>'transaction created',
            'data' => [
                'transaction' => $this->transactionFormat($transaction)
            ],
        ],201);
    }

    public function getTransaction(Request $request) {
        // belum lengkap
        $user = $request->user;
        if($user->role == 'admin'){
            $data = Transaction::all();
            return response()->json([
                'success' => true,
                'message' => 'All Transaction',
                'data' => [
                    'transactions' => $this->transactionArrayFormat($data)
                ],
            ], 200);
        } else {
            $data = Transaction::where('user_id', $user->id)->get();
            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => 'All Transaction',
                    'data' => [
                        'transactions' => $this->transactionArrayFormat($data)
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

        }

        if(!$user){
            $data = Transaction::find($user->Id);
            return response()->json([
                'success' => true,
                'message' => 'Transaction for user id: '+$data->id,
                'data' => [
                    'transactions' => $data,
                ],
            ], 200);
        }
    }

    public function getTransactionById($transactionId) {
        $data = Transaction::find($transactionId);
        if($data){
             return response()->json([
                'success' => true,
                'message' => 'Transaction found',
                'data' => [
                    'transaction' => $this->transactionFormat($data)
                ],
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
              ], 404);
        }
    }

    public function updateTransaction(Request $request, $transactionId){
        $data = Transaction::where('id',$transactionId)->update([
            'deadline'=>null
        ]);
        if($data){
            return response()->json([
                'success'=>true,
                'message'=>'Transaction has been updated',
                'data' => [
                    'transactions' => $this->transactionFormat($data)
                ],
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
              ], 404); 
        }
    }
    
    // TODO: Create transaction logic
    private function transactionFormat($transaction) {
        $user = User::find($transaction->user_id);
        $book = Book::find($transaction->book_id);
        return [
            'user' => [
                'name' => $user->name,
                'email' => $user->email
            ],
            // 'book' => $book,
            'book' => [
                'title' => $book->title,
                'author' => $book->author,
                'description' => $book->description,
                'synopsis' => $book->synopsis
            ],
            'deadline' => $transaction->deadline,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at
        ];
    }

    private function transactionArrayFormat($transactions) {
        $data = [];

        foreach($transactions as $transaction) {
            array_push($data, $this->transactionFormat($transaction));
        }

        return $data;
    }
}
