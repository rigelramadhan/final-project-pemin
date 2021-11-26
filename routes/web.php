<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', ['uses' => 'AuthController@register']); // SUDAH

    $router->post('/login', ['uses' => 'AuthController@login']); // SUDAH
});

$router->group(['prefix' => 'books'], function () use ($router) {
    $router->get('/', ['uses' => 'BookController@getBooks']); // SUDAH

    $router->get('/{bookId}', ['uses' => 'BookController@getBookById']); // SUDAH
});

$router->group(['middleware' => 'userAuth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/{userId}', ['uses' => 'UserController@getUserById']); // SUDAH

        $router->put('/{userId}', ['uses' => 'UserController@updateUser']); // SUDAH

        $router->delete('/{userId}', ['uses' => 'UserController@deleteUser']); // HAMPIR SUDAH
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->get('/', ['uses' => 'TransactionController@getTransaction']); //BELUM LENGKAP

        $router->get('/{transactionId}', ['uses' => 'TransactionController@getTransactionById']); //SUDAH
    });
});

$router->group(['middleware' => 'adminAuth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', ['uses' => 'UserController@getUsers']); 
    });

    $router->group(['prefix' => 'books'], function () use ($router) {
        $router->post('/', ['uses' => 'BookController@createBook']); //BELUM LENGKAP

        $router->put('/{bookId}', ['uses' => 'BookController@updateBook']); //BELUM LENGKAP

        $router->delete('/{bookId}', ['uses' => 'BookController@deleteBook']); //BELUM LENGKAP
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->put('/{transactionId}', ['uses' => 'TransactionController@updateTransaction']); //SUDAH
    });
});

$router->group(['middleware' => 'auth:user'], function () use ($router) {
    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->post('/', ['uses' => 'TransactionController@createTransaction']); //BELUM LENGKAP
    });
});
