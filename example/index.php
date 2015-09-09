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

class Smiley
{
    public function handle($message, $reply)
    {
        $reply->content .= " :)";
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


$app_id = 'wx4dd294ec95425923';
$token = '3msP9AVToxjVsrHEzaiG';

$server = new Server($app_id, $token);

$server->on('message.text')
    ->then('Msg@handle')
    ->then('Smiley@handle');

$server->on('event.subscribe')
    ->then('Sub@handle');

$server->on('message.image')
    ->then(function($message, $reply){
        $reply->type = 'image';
        $reply->image = (object) ['MediaId' => $message->media];
    });

$server->run();