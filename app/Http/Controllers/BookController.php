<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

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
        $data = Book::create($request->all());
        return response()->json([
            'success'=>true,
            'message'=>'response search book',
            'data' => [
                'book' => [
                        'id'=>$data->id,
                        'title'=>$data->data,
                        'description'=>$data->description,
                        'author'=>$data->author,
                        'year'=>$data->year,
                        'synopsis'=>$data->sysnopsis,
                        'stock'=>$data->stock,
                ],
            ],
        ],200);
    }

    public function getBooks() {
        // TODO: Lengkapin getBooks
        $book = Book::all();
        if ($book->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Book not available'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'All books found',
            'data' => [
                'book' => [
                    'title' => $book->title,
                    'description' => $book->description,
                    'author' => $book->author,
                    'year' => $book->year,
                    'synopsis' => $book->synopsis,
                    'stock' => $book->stock
                ]
            ]
        ], 200);
    }

    public function getBookById($bookId) {
        // TODO: Lengkapin getBookById
        $book = Book::find($bookId);

        if($book){
            return response()->json([
                'success' => true,
                'message' => 'A book found',
                'data' => [
                    'book' => [
                        'title' => $book->title,
                        'description' => $book->description,
                        'author' => $book->author,
                        'year' => $book->year,
                        'synopsis' => $book->synopsis,
                        'stock' => $book->stock
                    ]
                ]
              ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'A book not found'
              ], 400);
        }  
    }

    public function updateBook(Request $request, $id){
        $data= Book::where('id',$id)->update([
            'id'=>$data->id,
            'title'=>$data->data,
            'description'=>$data->description,
            'author'=>$data->author,
            'year'=>$data->year,
            'synopsis'=>$data->sysnopsis,
            'stock'=>$data->stock,
        ]);
        if($data){
            return response()->json([
                'success'=>true,
                'message'=>'Book has been updated',
                'data' => [
                    'book' => [
                            'id'=>$data->id,
                            'title'=>$data->data,
                            'description'=>$data->description,
                            'author'=>$data->author,
                            'year'=>$data->year,
                            'synopsis'=>$data->sysnopsis,
                            'stock'=>$data->stock,
                    ],
                ],
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'A book not found'
              ], 400);
        }
    }

    public function deleteBook($id){
        $data=Book::find($id);
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
              ], 400);
        }
    }
}
