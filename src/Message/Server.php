<?php

namespace Zhibaihe\WeChat\Message;

use Illuminate\Http\Request;

/**
 * 接收来自微信服务器的消息/事件推送并处理之
 */
class Server
{
	/**
	 * 微信公众号 app_id
	 * @var string
	 */
	protected $app_id;

	/**
	 * 微信公众号 token
	 * @var string
	 */
	protected $token;

	/**
	 * 安全模式下的加密密钥
	 *
	 * 可选。若为 `null`，则消息传递模式为明文模式，长度必须为 43 字符
	 * @var string
	 */
	protected $AES_key;

	/**
	 * 用于处理消息打包／解包的工具类
	 * @var Zhibaihe\WeChat\Message\Messager
	 */
	protected $messager;

	/**
	 * 消息处理流水线
	 * @var Zhibaihe\WeChat\Message\Pipeline
	 */
	protected $pipeline;

	public function __construct($app_id, $token, $AES_key = null)
	{
		$this->app_id = $app_id;
		$this->token = $token;
		$this->AES_key = $AES_key;

		$this->messager = new Messager($app_id, $token, $AES_key);
	}

	public function pipeline(Pipeline $pipeline)
	{
		$this->pipeline = $pipeline;
	}

	/**
	 * 启动消息接收服务
	 * 
	 * @return void
	 */
	public function run()
	{
		$request = Request::capture();

		if($request->method() == 'GET')
		{
            die($this->echostr($request->get('echostr'),
                $request->get('signature'),
                $request->get('timestamp'),
                $request->get('nonce')
            ));
		}

        $content = $request->getContent();

        extract($request->only('msg_signature', 'timestamp', 'nonce'));

        $message = $this->messager->receive($msg_signature, $timestamp, $nonce, $content);

        $reply = $this->pipeline->process($message);

        $response = $this->messager->prepare($reply, $timestamp, $nonce);

        error_log($response);
		
		echo $response;
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
        $valid = $this->messager->validate($signature, $timestamp, $nonce);

        if (!$valid)
        {
        	die();
        }

        return $echostr;
    }
}