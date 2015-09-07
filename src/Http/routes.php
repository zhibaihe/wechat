<?php

$namespace = 'Zhibaihe\WeChat\Http\Controllers';

get('/wechat/entrance', ['as' => 'wechat.entrance', 'uses' => "$namespace\ValidateController@ping"]);

post('/wechat/entrance', "$namespace\MessageController@message");