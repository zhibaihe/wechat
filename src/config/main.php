<?php

return [
	/**
	 * 消息传递模式
	 *
	 * 1. naked       - 明文模式
	 * 2. compatible  - 兼容模式
	 * 3. safe        - 安全模式
	 */
	'mode'   => 'naked',

	/**
	 * 微信公众平台 token
	 */
	'token'  => env('WECHAT_TOKEN',  '...'),

	/**
	 * 微信公众平台 EncodingAESKey
	 */
	'AESKey' => env('WECHAT_AESKEY', '...'),

	/**
	 * 微信公众平台 app_id
	 */
	'app_id' => env('WECHAT_APP_ID', '...'),

	/**
	 * 微信公众平台 app_secret
	 */
	'app_secret' => env('WECHAT_APP_SECRET', '...')
];