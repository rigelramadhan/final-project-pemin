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
    $router->get('/', ['uses' => 'BookController@getBooks']); // TODO: Implementasiin getBooks()

    $router->get('/{bookId}', ['uses' => 'BookController@getBookById']); // TODO: Implementasiin getBookById($bookId)
});

$router->group(['middleware' => 'userAuth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/{userId}', ['uses' => 'UserController@getUserById']); // SUDAH

        $router->put('/{userId}', ['uses' => 'UserController@updateUser']); // SUDAH

        $router->delete('/{userId}', ['uses' => 'UserController@deleteUser']);
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->get('/', function () {
            // TODO: Routes this to the right controller
        });

        $router->get('/{transactionId}', function () {
            // TODO: Routes this to the right controller
        });
    });
});

$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', function () {
            // TODO: Routes this to the right controller
        });
    });

    $router->group(['prefix' => 'books'], function () use ($router) {
        $router->post('/', function () {
            // TODO: Routes this to the right controller
        });

        $router->put('/{bookId}', function () {
            // TODO: Routes this to the right controller
        });

        $router->delete('/{bookId}', function () {
            // TODO: Routes this to the right controller
        });
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->put('/{transactionId}', function () {
            // TODO: Routes this to the right controller
        });
    });
});

$router->group(['middleware' => 'auth:user'], function () use ($router) {
    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->post('/', function () {
            // TODO: Routes this to the right controller
        });
    });
});
