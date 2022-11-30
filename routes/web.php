<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\AttrsController;

/*https://www.facebook.com/photo/?fbid=10154038637712376&set=pob.100000064412701
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
$router->get('/attrs/{attr_id:[0-9]+}', 'AttrsController@properties');
$router->get('/attrs/{attr_id:[0-9]+}/values/tree/{value_id:[0-9]+}', 'AttrsController@treeNodes');
$router->get('/attrs/{attr_id:[0-9]+}/values/{value_id:[0-9]+}', 'AttrsController@value');
$router->get('/attrs/{attr_id:[0-9]+}/values', 'AttrsController@table');
$router->get('/attrs/{attr_id:[0-9]+}/related/{value_id:[0-9]+}', 'AttrsController@relatedTable');
$router->get('/attrs/{attr_id:[0-9]+}/values/list', 'AttrsController@values');
$router->post('/attrs/{attr_id:[0-9]+}/title', 'AttrsController@setTitle');
$router->post('/attrs/{attr_id:[0-9]+}/values/remove', 'AttrsController@remove');
$router->post('/attrs/{attr_id:[0-9]+}/values/add', 'AttrsController@addRecord');
$router->post('/attrs/{attr_id:[0-9]+}/values/{value_id:[0-9]+}/edit', 'AttrsController@editRecord');
$router->post('/attrs/values/edit', 'AttrsController@editValue');

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
