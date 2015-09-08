<?php

namespace Zhibaihe\WeChat\Http\Controllers;

use Illuminate\Http\Request;
use Zhibaihe\WeChat\Server;

class MessageController extends Controller
{
	protected $wechat;

	public function __construct(Server $wechat)
	{
		$this->wechat = $wechat;
	}

	public function message(Request $request)
	{
        $content = $request->getContent();

        $msg_signature = $request->get('msg_signature');
        $timestamp = $request->get('timestamp');
        $nonce = $request->get('nonce');

        $message = $this->wechat->receive($msg_signature, $timestamp, $nonce, $content);

        $reply = [
            'ToUserName'   => $message['FromUserName'],
            'FromUserName' => $message['ToUserName'],
            'CreateTime'   => time(),
            'MsgType'      => 'text',
            'Content'      => $message['Content'],
        ];

        $response = $this->wechat->prepare($reply, $timestamp, $nonce);

        return $response;
	}
}