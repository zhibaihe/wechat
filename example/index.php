<?php

include(__DIR__ . '/../vendor/autoload.php');

use Zhibaihe\WeChat\Message\Server;
use Zhibaihe\WeChat\Message\Pipeline;

$pipeline = new Pipeline();

$pipeline->attach('message.text', function($message, $reply){
    $reply = [
        'ToUserName'   => $message['FromUserName'],
        'FromUserName' => $message['ToUserName'],
        'CreateTime'   => time(),
        'MsgType'      => 'text',
        'Content'      => $message['Content'] . ' via Pipeline',
    ];
	return $reply;
});
$pipeline->attach('event.subscribe', function($message, $reply){
    $reply = [
        'ToUserName'   => $message['FromUserName'],
        'FromUserName' => $message['ToUserName'],
        'CreateTime'   => time(),
        'MsgType'      => 'text',
        'Content'      => 'Welcome onboard!'
    ];
	return $reply;
});


$app_id = 'wx4dd294ec95425923';
$token = '3msP9AVToxjVsrHEzaiG';

$server = new Server($app_id, $token);

$server->pipeline($pipeline);

$server->run();