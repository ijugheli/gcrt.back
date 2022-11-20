<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\AttrsController;

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

$router->get('/attrs', 'AttrsController@list');
$router->get('/attrs/{attr_id:[0-9]+}', 'AttrsController@details');
$router->get('/attrs/{attr_id:[0-9]+}/values', 'AttrsController@full');
$router->get('/attrs/values/{attr_id:[0-9]+}', 'AttrsController@values');
$router->post('/attrs/{attr_id:[0-9]+}/values/remove', 'AttrsController@remove');
$router->post('/attrs/{attr_id:[0-9]+}/values/add', 'AttrsController@addValues');

//Authorization
$router->post('/user/login', 'AuthController@login');
$router->post('/user/logout', 'AuthController@logout');
$router->post('/user/refresh', 'AuthController@refresh');
$router->get('/user/profile', 'AuthController@me');

//User Management
$router->get('/user/list', 'UserController@list');
$router->get('/user/{user_id:[0-9]+}', 'UserController@details');
$router->post('/user/add', 'UserController@add');
$router->post('/user/edit/{user_id:[0-9]+}', 'UserController@edit');
$router->post('/user/changePassword', 'UserController@changePassword');
$router->delete('/user/{user_id:[0-9]+}', 'UserController@delete');