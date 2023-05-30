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

$router->get('test2', 'AttrsController@test');
$router->get('test1', function () use ($router) {
    return $router->app->version();
});


$router->group(['middleware' => 'auth', 'prefix' => $backendPrefix], function () use ($router) {
    $router->group(['prefix' => '/client'], function () use ($router) {
        $router->get('/index', 'ClientController@index');
        $router->get('/show/{id:[0-9]+}', 'ClientController@show');
        $router->post('/store', 'ClientController@store');
        $router->delete('/destroy/{id:[0-9]+}', 'ClientController@destroy');
    });

    $router->group(['prefix' => '/case'], function () use ($router) {
        $router->post('/store', 'Case\CaseController@store');
        $router->get('/index', 'Case\CaseController@index');
        $router->get('/show/{id:[0-9]+}', 'Case\CaseController@show');
        $router->delete('/destroy/{id:[0-9]+}', 'Case\CaseController@destroy');
        $router->get('/clients', 'Case\CaseController@getClients');
        $router->get('/case-managers', 'Case\CaseController@getCaseManagers');

        $router->group(['prefix' => '/diagnosis'], function () use ($router) {
            $router->get('/index', 'Case\DiagnosisController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\DiagnosisController@show');
            $router->post('/store', 'Case\DiagnosisController@store');
            $router->post('/update', 'Case\DiagnosisController@update');
            $router->delete('/destroy/{id:[0-9]+}', 'Case\DiagnosisController@destroy');
        });
        $router->group(['prefix' => '/referral'], function () use ($router) {
            $router->get('/index', 'Case\ReferralController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\ReferralController@show');
            $router->post('/store', 'Case\ReferralController@store');
            $router->post('/update', 'Case\ReferralController@update');
            $router->delete('/destroy/{id:[0-9]+}', 'Case\ReferralController@destroy');
        });
        $router->group(['prefix' => '/consultation'], function () use ($router) {
            $router->get('/index', 'Case\ConsultationController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\ConsultationController@show');
            $router->post('/store', 'Case\ConsultationController@store');
            $router->post('/update', 'Case\ConsultationController@update');
            $router->delete('/destroy/{id:[0-9]+}', 'Case\ConsultationController@destroy');
        });
        $router->group(['prefix' => '/forms-of-violence'], function () use ($router) {
            $router->get('/index', 'Case\FormsOfViolenceController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\FormsOfViolenceController@show');
            $router->post('/store', 'Case\FormsOfViolenceController@store');
            $router->post('/update/{case_id:[0-9]+}', 'Case\FormsOfViolenceController@update');
            $router->delete('/destroy/{id:[0-9]+}', 'Case\FormsOfViolenceController@destroy');
        });
        $router->group(['prefix' => '/care-plan'], function () use ($router) {
            $router->get('/index', 'Case\CarePlanController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\CarePlanController@show');
            $router->post('/store', 'Case\CarePlanController@store');
            $router->post('/update/{case_id:[0-9]+}', 'Case\CarePlanController@update');
            $router->delete('/destroy/{id:[0-9]+}', 'Case\CarePlanController@destroy');
        });
        $router->group(['prefix' => '/mental-symptom'], function () use ($router) {
            $router->get('/index', 'Case\MentalSymptomController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\MentalSymptomController@show');
            $router->post('/store', 'Case\MentalSymptomController@store');
            $router->post('/update/{case_id:[0-9]+}', 'Case\MentalSymptomController@update');
            $router->post('/destroy/{case_id:[0-9]+}', 'Case\MentalSymptomController@destroy');
        });
        $router->group(['prefix' => '/somatic-symptom'], function () use ($router) {
            $router->get('/index', 'Case\SomaticSymptomController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\SomaticSymptomController@show');
            $router->post('/store', 'Case\SomaticSymptomController@store');
            $router->post('/update/{case_id:[0-9]+}', 'Case\SomaticSymptomController@update');
            $router->post('/destroy/{case_id:[0-9]+}', 'Case\SomaticSymptomController@destroy');
        });
        $router->group(['prefix' => '/other-symptom'], function () use ($router) {
            $router->get('/index', 'Case\OtherSymptomController@index');
            $router->get('/show/{id:[0-9]+}', 'Case\OtherSymptomController@show');
            $router->post('/store', 'Case\OtherSymptomController@store');
            $router->post('/update/{case_id:[0-9]+}', 'Case\OtherSymptomController@update');
            $router->delete('/destroy/{id:[0-9]+}', 'Case\OtherSymptomController@destroy');
        });
    });

    $router->group(['prefix' => '/attrs'], function () use ($router) {
        $router->post('test/test', 'AttrsController@test');
        $router->get('', 'AttrsController@list');
        $router->post('/add', 'AttrsController@addAttribute');
        $router->delete('/{attr_id:[0-9]+}', 'AttrsController@removeAttribute');
        $router->get('/static', 'AttrsController@attrs');
        $router->get('/static/{attr_id:[0-9]+}', 'AttrsController@getAttrStatic');
        $router->get('/{attr_id:[0-9]+}', 'AttrsController@properties');
        $router->get('/{attr_id:[0-9]+}/values/tree/{value_id:[0-9]+}', 'AttrsController@treeNodes');
        $router->get('/{attr_id:[0-9]+}/values/tree-select/{value_id:[0-9]+}', 'AttrsController@treeNodes');
        $router->get('/{attr_id:[0-9]+}/values/{value_id:[0-9]+}', 'AttrsController@value');
        $router->get('/{attr_id:[0-9]+}/values', 'AttrsController@table');
        $router->get('/tree-select-options', 'AttrsController@getTreeselectOptions');
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
    $router->group(['prefix' => '/users'], function () use ($router) {
        $router->get('/list', 'UserController@list');
        $router->get('/profile', 'UserController@me');
        $router->get('/{user_id:[0-9]+}', 'UserController@details');
        $router->post('/add', 'UserController@add');
        $router->post('/edit/{user_id:[0-9]+}', 'UserController@edit');
        $router->post('/changePassword', 'UserController@changePassword');
        $router->delete('/{user_id:[0-9]+}', 'UserController@delete');
        $router->post('/update-boolean-properties/{user_id:[0-9]+}', 'UserController@updateBooleanProperties');
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

$router->group(['prefix' => $backendPrefix], function () use ($router) {
    $router->post('/survey', 'SurveyController@create');
    $router->get('/survey/list', 'SurveyController@list');
    $router->post('/survey/store', 'SurveyController@store');
    $router->get('/survey/{survey_id}', 'SurveyController@getSurvey');


    $router->get('test', 'SurveyController@test');
    $router->get('user/report', 'UserController@getReports');
});
// $router->group(['prefix' => '$backendPrefix'], function () use ($router) {
// });
