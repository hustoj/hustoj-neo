<?php

app('router')->get('/api/data', 'Judge\ApiController@data')->middleware('zip.response');
app('router')->post('/api/report', 'Judge\ApiController@report');
app('router')->post('/api/heartbeat', 'Judge\ApiController@heartbeat');
