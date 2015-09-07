<?php

namespace Zhibaihe\WeChat\Http\Controllers;

use Illuminate\Http\Request;
use Zhibaihe\WeChat\WeChatServiceInterface;

use Log;

class ValidateController extends Controller
{
	protected $wechat;

	public function __construct(WeChatServiceInterface $wechat)
	{
		$this->wechat = $wechat;
	}
	public function ping(Request $request)
	{
		$echostr = $request->get('echostr');

		Log::info('Test', $request->all());

        if ($echostr != '')
        {
            return $this->echostr($echostr,
                $request->get('signature'),
                $request->get('timestamp'),
                $request->get('nonce')
            );
        }
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