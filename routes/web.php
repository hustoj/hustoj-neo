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

app('router')->get('/', ['as' => 'home', 'uses' => 'Web\HomeController@index']);

app('router')->get('/problemset', ['as' => 'problem.index', 'uses' => 'Web\ProblemController@index']);
app('router')->get('/problem/{problem}', ['as' => 'problem.view', 'uses' => 'Web\ProblemController@show']);
app('router')->get('/problem/{problem}/summary', ['as' => 'problem.summary', 'uses' => 'Web\ProblemController@summary']);

app('router')->get('/contest', ['as' => 'contest.index', 'uses' => 'Web\ContestController@index']);

app('router')->group(['middleware' => 'authorizeContest'], function () {
    app('router')->get('/contest/{contest}', ['as' => 'contest.view', 'uses' => 'Web\ContestController@show']);
    app('router')->get('/contest/{contest}/standing', ['as' => 'contest.standing', 'uses' => 'Web\ContestController@standing']);
    app('router')->get('/contest/{contest}/status', ['as' => 'contest.status', 'uses' => 'Web\ContestController@status']);
    app('router')->get('/contest/{contest}/submit', ['as' => 'contest.submit', 'uses' => 'Web\ContestController@submit']);
    app('router')->get('/contest/{contest}/clarify', ['as' => 'contest.clarify', 'uses' => 'Web\ContestController@clarify']);
    app('router')->get('/contest/{contest}/problem/{order}', ['as' => 'contest.problem', 'uses' => 'Web\ContestController@problem']);
});
app('router')->get('/clarify', ['as' => 'topic.list', 'uses' => 'Web\TopicController@index']);

app('router')->get('/topic/create', ['as' => 'topic.create', 'uses' => 'Web\TopicController@create', 'middleware' => 'auth']);
app('router')->post('/topic/store', ['as' => 'topic.store', 'uses' => 'Web\TopicController@store', 'middleware' => 'auth']);
app('router')->get('/topic/{id}', ['as' => 'topic.view', 'uses' => 'Web\TopicController@show']);
app('router')->post('/topic/{id}', ['as' => 'topic.reply', 'uses' => 'Web\TopicController@reply', 'middleware' => 'auth']);

app('router')->get('/rank', ['as' => 'user.index', 'uses' => 'Web\UserController@index']);
app('router')->get('/profile/', ['as' => 'user.profile', 'uses' => 'Web\UserController@profile']);
app('router')->post('/profile/edit', ['as' => 'user.edit', 'uses' => 'Web\UserController@edit', 'middleware' => 'auth']);
app('router')->post('/profile/password', ['as' => 'user.password', 'uses' => 'Web\UserController@password', 'middleware' => 'auth']);
app('router')->get('/profile/password', ['as' => 'user.password', 'uses' => 'Web\UserController@editPassword', 'middleware' => 'auth']);
app('router')->get('/user/{username}', ['as' => 'user.view', 'uses' => 'Web\UserController@show']);

app('router')->get('/status', ['as' => 'solution.index', 'uses' => 'Web\SolutionController@index']);
app('router')->get('/solution/{solution}/source', ['as' => 'solution.source', 'uses' => 'Web\SolutionController@source', 'middleware' => 'auth']);
app('router')->get('/solution/{solution}/compileinfo', ['as' => 'solution.compile', 'uses' => 'Web\SolutionController@compileInfo', 'middleware' => 'auth']);
app('router')->get('/solution/{solution}/runtimeinfo', ['as' => 'solution.runtime', 'uses' => 'Web\SolutionController@runtimeInfo', 'middleware' => 'auth']);
app('router')->get('/problem/{problem}/submit', ['as' => 'problem.submit', 'uses' => 'Web\SolutionController@create', 'middleware' => 'auth']);
app('router')->post('/solution/store', ['as' => 'solution.store', 'uses' => 'Web\SolutionController@store', 'middleware' => 'auth']);

// Single Pages
app('router')->get('{page}', [
    'as' => 'pages', 'uses' => function ($page) {
        $template = 'web.pages.'.$page;

        return view($template);
    },
])->where('page', 'contact|about|faqs');

app('router')->auth();

$this->get('logout', 'Auth\LoginController@logout');
