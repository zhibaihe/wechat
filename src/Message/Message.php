<?php

namespace Zhibaihe\WeChat\Message;

class Message
{
	protected $attributes;

	public function __construct($attrs)
	{
		$this->attributes = $attrs;
	}

	public function toArray()
	{
		return $this->attributes;
	}

	public function __get($name)
	{
		if( ! array_key_exists($name, $this->attributes) )
		{
			return null;
		}

		return $this->attributes[$name];
	}

	public function __set($name, $value)
	{
		if(array_key_exists($name, $this->attributes))
		{
			$this->attributes[$name] = $value;
			return true;
		}

		return false;
	}
}