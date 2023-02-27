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



$router->group(['middleware' => 'auth', 'prefix' => $backendPrefix], function () use ($router) {
    $router->group(['prefix' => '/attrs'], function () use ($router) {
        $router->get('', 'AttrsController@list');
        $router->post('/add', 'AttrsController@addAttribute');
        $router->delete('/{attr_id:[0-9]+}', 'AttrsController@removeAttribute');
        $router->get('/static', 'AttrsController@attrs');
        $router->get('/{attr_id:[0-9]+}', 'AttrsController@properties');
        $router->get('/{attr_id:[0-9]+}/values/tree/{value_id:[0-9]+}', 'AttrsController@treeNodes');
        $router->get('/{attr_id:[0-9]+}/values/{value_id:[0-9]+}', 'AttrsController@value');
        $router->get('/{attr_id:[0-9]+}/values', 'AttrsController@table');
        $router->get('/{attr_id:[0-9]+}/related/{value_id:[0-9]+}', 'AttrsController@relatedTable');
        $router->get('/{attr_id:[0-9]+}/values/list', 'AttrsController@values');
        $router->post('/{attr_id:[0-9]+}/title', 'AttrsController@setTitle');
        $router->post('/{attr_id:[0-9]+}/values/remove', 'AttrsController@remove');
        $router->post('/{attr_id:[0-9]+}/values/add', 'AttrsController@addRecord');
        $router->post('/{attr_id:[0-9]+}/values/{value_id:[0-9]+}/edit', 'AttrsController@editRecord');
        $router->post('/values/edit', 'AttrsController@editValue');
        // records
        $router->get('/{attr_id:[0-9]+}/records', 'AttrsController@records');
        $router->post('/{attr_id:[0-9]+}/update', 'AttrsController@updateAttr');

        $router->post('/{attr_id:[0-9]+}/properties/reorder', 'PropertyController@reorderProperties');
        $router->post('/properties/add-section', 'PropertyController@addSection');
        $router->post('/properties/add-property', 'PropertyController@addProperty');
        $router->delete('/properties/{property_id:[0-9]+}', 'PropertyController@removeProperty');
        $router->post('/properties/{property_id:[0-9]+}/update', 'PropertyController@updateProperty');
    });


    // User Management
    $router->group(['prefix' => '/user'], function () use ($router) {
        $router->get('/list', 'UserController@list');
        $router->get('/profile', 'UserController@me');
        $router->get('/{user_id:[0-9]+}', 'UserController@details');
        $router->post('/add', 'UserController@add');
        $router->post('/edit/{user_id:[0-9]+}', 'UserController@edit');
        $router->post('/changePassword', 'UserController@changePassword');
        $router->delete('/{user_id:[0-9]+}', 'UserController@delete');
        $router->post('/update-boolean-properties/{user_id:[0-9]+}', 'UserController@updateBooleanColumns');
        $router->post('/permissions/{user_id:[0-9]+}/{attr_id:[0-9]+}', 'UserController@updatePermission');
    });
});

// Authorization
$router->group(['prefix' => $backendPrefix . '/user'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/logout', 'AuthController@logout');
    $router->post('/refresh', 'AuthController@refresh');
    $router->post('/send-recovery-link', 'AuthController@sendRecoveryLink');
    $router->post('/validate-code', 'AuthController@validateCode');
    //USER
    $router->post('/update-password', 'UserController@updatePassword');
});

$router->post('/survey', 'SurveyController@create');
$router->post('/survey/store', 'SurveyController@store');
$router->get('/survey/{attr_id}', 'SurveyController@getSurvey');

// $router->group(['prefix' => '$backendPrefix'], function () use ($router) {
// });
