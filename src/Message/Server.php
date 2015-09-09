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

	/**
	 * 消息种类
	 * 格式为：消息大类.消息类型 e.g. message.text, event.subscribe
	 * @var string 
	 */
	protected $messageRace;

	public function __construct($app_id, $token, $AES_key = null)
	{
		$this->app_id = $app_id;
		$this->token = $token;
		$this->AES_key = $AES_key;

		$this->messager = new Messager($app_id, $token, $AES_key);
		$this->pipeline = new Pipeline();
	}

	public function pipeline(Pipeline $pipeline)
	{
		$this->pipeline = $pipeline;
	}

	/**
	 * 设置当前消息种类，返回 `$this` 用于方法串接 (method chaining)
	 *
	 * @param  string $messageRace 消息种类。格式：大类.具体类型 e.g. message.text, event.subscribe
	 * @return Zhibaihe\WeChat\Message\Server $this
	 */
	public function on($messageRace)
	{
		$this->messageRace = $messageRace;

		return $this;
	}

	/**
	 * 添加消息处理函数，返回 `$this` 用于方法串接 (method chaining)
	 *
	 * @param  callable $callback 回调函数，或者 `class@method` 格式的字符串
	 */
	public function then($callback)
	{
		$this->pipeline->attach($this->messageRace, $callback);

		return $this;
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

        extract($request->only('msg_signature', 'timestamp', 'nonce'));
        $content = $request->getContent();

        $message = Factory::create($this->messager->receive($msg_signature, $timestamp, $nonce, $content));

        $reply = $this->pipeline->process($message);

        $response = $this->messager->prepare($reply->toArray(), $timestamp, $nonce);

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