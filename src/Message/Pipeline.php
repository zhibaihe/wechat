<?php

namespace Zhibaihe\WeChat\Message;

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
		$type = $message['MsgType'];

		if(in_array($type, ['text', 'image', 'audio', 'video', 'link', 'location']))
		{
			$type = "message.$type";
		}
		elseif($type == 'event')
		{
			$type = "event.{$message['Event']}";
		}

		error_log(sprintf("processing $type, with lines: %s", json_encode($this->lines)));

		error_log(count($this->lines[$type]));

		if( ! array_key_exists($type, $this->lines))
		{
			return $message;
		}

		$reply = $message;

		foreach($this->lines[$type] as $callback)
		{
			$reply = call_user_func_array($callback, [$message, $reply]);
		}

		return $reply;
	}
}