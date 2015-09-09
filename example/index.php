<?php

include(__DIR__ . '/../vendor/autoload.php');

use Zhibaihe\WeChat\Message\Server;
use Zhibaihe\WeChat\Message\Pipeline;

class Msg
{
    public function handle($message, $reply)
    {
        $reply->type = 'text';
        $reply->content = $message->content . ' via Pipeline';
    }
}

class Sub
{
    public function handle($message, $reply)
    {
        $reply->fill([
            'type'      => 'text',
            'content'   => 'Welcome onboard!',
        ]);
    }
}

function log_message($message)
{
    error_log(json_encode($message->toArray()));
}


$app_id = 'wx4dd294ec95425923';
$token = '3msP9AVToxjVsrHEzaiG';

$server = new Server($app_id, $token);

$server->on('message.text')
    ->tell('log_message');

$server->on('message.text')
    ->reply('Msg@handle');

$server->on('event.subscribe')
    ->reply('Sub@handle');

$server->on('message.image')
    ->reply(function ($message, $reply) {
        $reply->type = 'image';
        $reply->image = (object) ['MediaId' => $message->media];
    });

$server->run();
