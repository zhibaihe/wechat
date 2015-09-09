<?php

/*
 * This file is part of the non-official WeChat SDK developed by Zhiyan.
 *
 * (c) DUAN Zhiyan <zhiyan@zhibaihe.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
     * 消息监听器
     * @var array
     */
    protected $listeners = array();

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
     * 设置 `$callback` 为该类型消息的唯一处理函数
     * 若此前该类型消息已有消息处理函数，这些函数将被丢弃
     *
     * @param callable $callback 回调函数，或者 `class@method` 格式的字符串
     */
    public function reply($callback)
    {
        $this->pipeline->flush($this->messageRace);
        $this->pipeline->attach($this->messageRace, $callback);
    }

    /**
     * 将 `$callback` 函数登记为当前类型消息的监听器
     * 一旦收到此类消息，将通过 `broadcast()` 方法通知之
     *
     * @param callable $callback 回调函数，接受一个参数: 收到的消息
     */
    public function tell($callback)
    {
        if (! array_key_exists($this->messageRace, $this->listeners)) {
            $this->listeners[$this->messageRace] = array();
        }

        $this->listeners[$this->messageRace][] = $callback;
    }

    /**
     * 将收到的消息逐个通知已经登记的监听器
     *
     * @param Message $message 收到的消息
     */
    public function broadcast(Message $message)
    {
        if (array_key_exists($message->race(), $this->listeners)) {
            foreach ($this->listeners[$message->race()] as $callback) {
                call_user_func($callback, $message);
            }
        }
    }

    /**
     * 启动消息接收服务
     *
     * @param string $content 来自微信服务器的 HTTP 请求的 content
     *                        若留空则自动从 `php://input` 流中读取
     *
     * @return void
     */
    public function run($content = null)
    {
        extract($this->capture());

        if ($method === 'GET') {
            die($this->echostr($echostr,
                $signature,
                $timestamp,
                $nonce
            ));
        }

        $content = $content !== null ?: file_get_contents('php://input');

        $message = Factory::create($this->messager->receive($msg_signature, $timestamp, $nonce, $content));

        $this->broadcast($message);
        $reply = $this->pipeline->process($message);

        $response = $this->messager->prepare($reply->toArray(), $timestamp, $nonce);

        echo $response;
    }

    /**
     * 微信 API endpoint 验证请求
     *
     * @param $echostr
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return
     */
    protected function echostr($echostr, $signature, $timestamp, $nonce)
    {
        $valid = $this->messager->validate($signature, $timestamp, $nonce);

        if (!$valid) {
            die();
        }

        return $echostr;
    }

    /**
     * 抓取来自微信服务器的请求中的有用参数
     *
     * @return array 消息参数
     */
    protected function capture()
    {
        $request = array(
            'method' => $_SERVER['REQUEST_METHOD'],
        );

        $vars = array('echostr', 'signature', 'msg_signature', 'timestamp', 'nonce');

        foreach ($vars as $var) {
            $request[$var] = array_key_exists($var, $_GET)
                ? $_GET[$var] : '';
        }

        return $request;
    }
}
