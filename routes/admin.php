<?php

app('router')->get('/home', ['as' => 'admin.home', 'uses' => 'Admin\HomeController@dashboard']);
app('router')->get('/home/chart', ['as' => 'home_data', 'uses' => 'Admin\HomeController@chart']);

app('router')->resource('problems', 'Admin\ProblemController');
app('router')->get('problems/{id}/files', 'Admin\ProblemController@dataFiles');
app('router')->get('problems/{id}/files/{filename}', 'Admin\ProblemController@getFile');
app('router')->delete('problems/{id}/files/{filename}', 'Admin\ProblemController@removeFile');
app('router')->post('problems/{id}/upload', 'Admin\ProblemController@upload');
app('router')->resource('contests', 'Admin\ContestController');
app('router')->get('contests/{id}/problems', 'Admin\ContestController@problems');
app('router')->get('contests/{id}/users', 'Admin\ContestController@users');
app('router')->resource('users', 'Admin\UserController');
app('router')->resource('user/logging', 'Admin\User\LoggingController');
app('router')->resource('articles', 'Admin\ArticleController');
app('router')->resource('topics', 'Admin\TopicController');
app('router')->resource('roles', 'Admin\RoleController');
app('router')->resource('options', 'Admin\OptionController');
