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

$backendPrefix = '';
// $backendPrefix = '/api';

$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->get($backendPrefix . '/attrs', 'AttrsController@list');
$router->get($backendPrefix . '/attrs/static', 'AttrsController@attrs');
$router->get($backendPrefix . '/attrs/{attr_id:[0-9]+}', 'AttrsController@properties');
$router->get($backendPrefix . '/attrs/{attr_id:[0-9]+}/values/tree/{value_id:[0-9]+}', 'AttrsController@treeNodes');
$router->get($backendPrefix . '/attrs/{attr_id:[0-9]+}/values/{value_id:[0-9]+}', 'AttrsController@value');
$router->get($backendPrefix . '/attrs/{attr_id:[0-9]+}/values', 'AttrsController@table');
$router->get($backendPrefix . '/attrs/{attr_id:[0-9]+}/related/{value_id:[0-9]+}', 'AttrsController@relatedTable');
$router->get($backendPrefix . '/attrs/{attr_id:[0-9]+}/values/list', 'AttrsController@values');
$router->post($backendPrefix . '/attrs/{attr_id:[0-9]+}/title', 'AttrsController@setTitle');
$router->post($backendPrefix . '/attrs/{attr_id:[0-9]+}/values/remove', 'AttrsController@remove');
$router->post($backendPrefix . '/attrs/{attr_id:[0-9]+}/values/add', 'AttrsController@addRecord');
$router->post($backendPrefix . '/attrs/{attr_id:[0-9]+}/values/{value_id:[0-9]+}/edit', 'AttrsController@editRecord');
$router->post($backendPrefix . '/attrs/values/edit', 'AttrsController@editValue');

//Authorization
$router->post($backendPrefix . '/user/login', 'AuthController@login');
$router->post($backendPrefix . '/user/logout', 'AuthController@logout');
$router->post($backendPrefix . '/user/refresh', 'AuthController@refresh');
$router->get($backendPrefix . '/user/profile', 'AuthController@me');

//User Management
$router->get($backendPrefix . '/user/list', 'UserController@list');
$router->get($backendPrefix . '/user/{user_id:[0-9]+}', 'UserController@details');
$router->post($backendPrefix . '/user/add', 'UserController@add');
$router->post($backendPrefix . '/user/edit/{user_id:[0-9]+}', 'UserController@edit');
$router->post($backendPrefix . '/user/changePassword', 'UserController@changePassword');
$router->delete($backendPrefix . '/user/{user_id:[0-9]+}', 'UserController@delete');
