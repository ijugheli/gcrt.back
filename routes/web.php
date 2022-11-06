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
$router->get('/attrs/{attr_id}', 'AttrsController@details');
$router->get('/attrs/{attr_id}/values', 'AttrsController@full');
$router->get('/attrs/values/{attr_id}', 'AttrsController@values');
$router->post('/attrs/{attr_id}/values/remove', 'AttrsController@remove');
$router->post('/attrs/{attr_id}/values/add', 'AttrsController@addValues');

;
$router->post('/user/login', 'AuthController@login');
$router->post('/user/logout', 'AuthController@logout');
$router->post('/user/refresh', 'AuthController@refresh');
$router->post('/user/profile', 'AuthController@me');