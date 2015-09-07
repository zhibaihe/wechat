<?php

namespace Zhibaihe\WeChat\Http\Controllers;

use Illuminate\Http\Request;
use Zhibaihe\WeChat\WeChatServiceInterface;

class MessageController extends Controller
{
	protected $wechat;

	public function __construct(WeChatServiceInterface $wechat)
	{
		$this->wechat = $wechat;
	}

	public function message(Request $request)
	{
        $content = $request->getContent();

        $msg_signature = $request->get('msg_signature');
        $timestamp = $request->get('timestamp');
        $nonce = $request->get('nonce');

        $message = $this->wechat->decryptMessage($msg_signature, $timestamp, $nonce, $content);

        $reply = [
            'ToUserName'   => $message['FromUserName'],
            'FromUserName' => $message['ToUserName'],
            'CreateTime'   => time(),
            'MsgType'      => 'text',
            'Content'      => $message['Content'],
        ];

        $response = $this->wechat->encryptMessage($reply, $timestamp, $nonce);

        return $response;
	}

	/**
     * 微信 API endpoint 验证请求
     * @param $echostr
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return
     */
    protected function echostr($echostr, $signature, $timestamp, $nonce)
    {
        $valid = $this->wechat->validateSignature($signature, $timestamp, $nonce);

        if (!$valid)
        {
            \App::abort(403);
        }

        return $echostr;
    }
}