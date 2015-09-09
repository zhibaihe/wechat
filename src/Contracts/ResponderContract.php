<?php

namespace Zhibaihe\WeChat\Contracts;

use Zhibaihe\WeChat\Message\Message;

interface ResponderContract
{
	/**
	 * 响应何种类型的消息
	 *
	 * 消息类型为下列之一
	 * - text
     * - image
     * - voice
     * - video
     * - location
     * - link
     * - event
	 * 
	 * @return string 消息类型 
	 */
	public function respondeTo();

	public function responde(Message $message);
}