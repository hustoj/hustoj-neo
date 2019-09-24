<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', ['as' => 'home', 'uses' => 'Web\HomeController@index']);
Route::get('/home', ['uses' => 'Web\HomeController@index']);

Route::get('/problemset', [
    'as'   => 'problem.index',
    'uses' => 'Web\ProblemController@index',
]);
Route::get('/problem/{problem}', [
    'as'   => 'problem.view',
    'uses' => 'Web\ProblemController@show',
]);
Route::get('/problem/{problem}/summary', [
    'as'   => 'problem.summary',
    'uses' => 'Web\ProblemController@summary',
]);

Route::get('/contest', [
    'as'   => 'contest.index',
    'uses' => 'Web\ContestController@index',
]);

Route::group(['middleware' => 'authorizeContest'], function () {
    Route::get('/contest/{contest}', [
        'as'   => 'contest.view',
        'uses' => 'Web\ContestController@show',
    ]);
    Route::get('/contest/{contest}/standing', [
        'as'   => 'contest.standing',
        'uses' => 'Web\ContestController@standing',
    ]);
    Route::get('/contest/{contest}/status', [
        'as'   => 'contest.status',
        'uses' => 'Web\ContestController@status',
    ]);
    Route::get('/contest/{contest}/submit', [
        'as'   => 'contest.submit',
        'uses' => 'Web\ContestController@submit',
    ]);
    Route::get('/contest/{contest}/clarify', [
        'as'   => 'contest.clarify',
        'uses' => 'Web\ContestController@clarify',
    ]);
    Route::get('/contest/{contest}/problem/{order}', [
        'as'   => 'contest.problem',
        'uses' => 'Web\ContestController@problem',
    ]);
});
Route::get('/clarify', [
    'as'   => 'topic.list',
    'uses' => 'Web\TopicController@index',
]);

Route::get('/topic/create', [
    'as'         => 'topic.create',
    'uses'       => 'Web\TopicController@create',
    'middleware' => 'auth',
])->middleware('verified');
Route::post('/topic/store', [
    'as'         => 'topic.store',
    'uses'       => 'Web\TopicController@store',
    'middleware' => 'auth',
])->middleware('verified');
Route::get('/topic/{id}', [
    'as'   => 'topic.view',
    'uses' => 'Web\TopicController@show',
]);
Route::post('/topic/{id}', [
    'as'         => 'topic.reply',
    'uses'       => 'Web\TopicController@reply',
    'middleware' => 'auth',
])->middleware('verified');

Route::get('/rank', [
    'as'   => 'user.index',
    'uses' => 'Web\UserController@index',
]);
Route::get('/profile/', [
    'as'   => 'user.edit',
    'uses' => 'Web\UserController@profile',
]);
Route::post('/profile', [
    'as'         => 'user.edit',
    'uses'       => 'Web\UserController@edit',
    'middleware' => 'auth',
]);
Route::post('/profile/password', [
    'as'         => 'user.password',
    'uses'       => 'Web\UserController@password',
    'middleware' => 'auth',
]);
Route::get('/profile/password', [
    'as'         => 'user.password',
    'uses'       => 'Web\UserController@editPassword',
    'middleware' => 'auth',
]);
Route::get('/user/{username}', [
    'as'   => 'user.profile',
    'uses' => 'Web\UserController@show',
]);

Route::get('/status', [
    'as'   => 'solution.index',
    'uses' => 'Web\SolutionController@index',
]);
Route::get('/solution/{solution}/source', [
    'as'         => 'solution.source',
    'uses'       => 'Web\SolutionController@source',
    'middleware' => 'auth',
]);
Route::get('/solution/{solution}/compileinfo', [
    'as'         => 'solution.compile',
    'uses'       => 'Web\SolutionController@compileInfo',
    'middleware' => 'auth',
]);
Route::get('/solution/{solution}/runtimeinfo', [
    'as'         => 'solution.runtime',
    'uses'       => 'Web\SolutionController@runtimeInfo',
    'middleware' => 'auth',
]);
Route::get('/problem/{problem}/submit', [
    'as'         => 'problem.submit',
    'uses'       => 'Web\SolutionController@create',
    'middleware' => 'auth',
])->middleware('verified');
Route::post('/solution/store', [
    'as'         => 'solution.store',
    'uses'       => 'Web\SolutionController@store',
    'middleware' => 'auth',
])->middleware('verified');

// Single Pages
Route::get('{page}', [
    'as'   => 'pages',
    'uses' => function ($page) {
        $template = 'web.pages.'.$page;

        return view($template);
    },
])->where('page', 'contact|about|faqs');

Route::get('logout', 'Auth\LoginController@logout');

Auth::routes(['verify' => true]);
