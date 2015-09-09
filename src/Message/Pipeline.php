<?php

namespace Zhibaihe\WeChat\Message;

use Closure;

use Zhibaihe\WeChat\Exceptions\InvalidArgumentException;

/**
 * 消息处理流程
 *
 * 可通过此类别将多个处理逻辑组织成流水线
 * 消息将逐个经过流水线上的每一个模块
 */
class Pipeline
{
	protected $lines;

	public function __construct($configuration = [])
	{
		$this->lines = $configuration;
	}

	public function attach($type, $callable)
	{
		$callable = $this->parseCallable($callable);

		if( ! array_key_exists($type, $this->lines))
		{
			$this->lines[$type] = [];
		}

		$this->lines[$type][] = $callable;
	}

	public function detach($type, $callable)
	{
		if( ! array_key_exists($type, $this->lines))
			return;

		$this->lines[$type] = array_filter($this->lines, function($line) use ($callable){
			return $line == $callable;
		});
	}

	public function process($message)
	{
		$reply = new Message();

		$reply->from      = $message->to;
		$reply->to        = $message->from;
		$reply->timestamp = time();

		$race = $message->race();

		error_log("Processing: $race");

		if( ! array_key_exists($race, $this->lines))
		{
			return $reply;
		}

		error_log("Callbacks to be called: ". count($this->lines[$race]));

		foreach($this->lines[$race] as $callback)
		{
			call_user_func_array($callback, [$message, $reply]);
		}

		return $reply;
	}

	protected function parseCallable($callback)
	{
		if( is_object($callback) && ($callback instanceof Closure) )
		{
			return $callback;
		}

		$callback = ! is_string($callback) ?: explode('@', $callback);

		if( ! is_callable($callback) )
		{
			throw new InvalidArgumentException("Invalid callback {$callback}");
		}

		return $callback;
	}
}