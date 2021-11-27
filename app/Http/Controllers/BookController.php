<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BookController extends Controller
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

    public function createBook(Request $request){
        $validation = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
            'author' => 'required',
            'year' => 'required',
            'synopsis' => 'required',
            'stock' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fields cannot be empty.'
            ], 400);
        }
        $data = Book::create($request->all());
        return response()->json([
            'success'=>true,
            'message'=>'response search book',
            'data' => [
                'book' => $data
            ],
        ],201);
    }

    public function getBooks() {
        $book = Book::all();
        if ($book->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Book not available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'All books found',
            'data' => [
                'books' => $book
            ]
        ], 200);
    }

    public function getBookById($bookId) {
        $book = Book::find($bookId);

        if($book){
            return response()->json([
                'success' => true,
                'message' => 'A book found',
                'data' => [
                    'book' => $book
                ]
              ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'A book not found'
              ], 404);
        }  
    }

    public function updateBook(Request $request, $bookId){
        if(!Book::where('id',$bookId)){
            return response()->json([
                'success' => false,
                'message' => 'Book not found',
              ], 404);
        };
        if($request->user->role!=='admin'){
            return response()->json([
                'success' => false,
                'message' => 'Book cannot update',
              ], 403);
        };
        $data= Book::where('id',$bookId)->update($request->all());
        if($data){
            return response()->json([
                'success'=>true,
                'message'=>'Book has been updated',
                'data' => [
                    'book' => Book::where('id',$bookId)->first()
                ],
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'A book not found'
              ], 404);
        }
    }

    public function deleteBook($bookId){
        $data=Book::find($bookId);
        if($data){
            $data->delete();
            return response()->json([
                'success'=>true,
                'message'=>'Book has been deleted',
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'A book not found'
              ], 404);
        }
    }
}
