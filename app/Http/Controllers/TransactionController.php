<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
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
        $deadline = Carbon::now()->addWeek()->toDateString();

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
                'Transaction' => $this->transactionFormat($transaction)
            ],
        ],200);
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
                    'transaction' => $this->transactionArrayFormat($data)
                ],
            ], 200);
        } else {
            $data = Transaction::where('user_id', $user->id)->get();
            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => 'All Transaction',
                    'data' => [
                        'transaction' => $this->transactionArrayFormat($data)
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
                    'transaction' => $data,
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
                    'Transaction' => $this->transactionFormat($data)
                ],
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
              ], 404);
        }
    }

    public function updateTransaction($transactionId){
        $data = Transaction::where('id',$transactionId)->update([
            'deadline'=>null
        ]);
        if($data){
            return response()->json([
                'success'=>true,
                'message'=>'Transaction has been updated',
                'data' => [
                    'Transaction' => $this->transactionFormat($data)
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
                'author' => $book->author
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
