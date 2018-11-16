<?php

app('router')->get('/api/data', 'Judge\ApiController@data');
app('router')->post('/api/report', 'Judge\ApiController@report');
app('router')->post('/api/heartbeat', 'Judge\ApiController@heartbeat');
