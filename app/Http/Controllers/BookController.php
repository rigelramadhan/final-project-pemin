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
}
