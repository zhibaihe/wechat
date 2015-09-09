<?php

namespace Zhibaihe\WeChat\Message;

class Factory
{
	public static function create($attributes)
	{
		return new Message($attributes);
	}

	public static function make($race)
	{
		list($catgory, $type) = explode('.', $race);

		return new Message(self::$recipe[$race]);
	}

	protected static $recipe = [
		'message.text' => ['type' => 'text', 'content' => ''],
		'message.image' => ['type' => 'image', 'image' => ''],
	];
}