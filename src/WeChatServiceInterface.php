<?php

namespace Zhibaihe\WeChat;

interface WeChatServiceInterface
{
    /**
     * 对外部请求进行签名验证，以确定该请求是否来自微信服务器
     *
     * 三个参数均出自来自微信服务器的调用
     *
     * @param string $signature 待验证的签名
     * @param string $timestamp 时间戳
     * @param string $nonce 一次性随机串
     *
     * @return boolean 签名的有效性
     */
    public function validate($signature, $timestamp, $nonce);

	/**
	 * 将公众平台回复用户的消息进行打包.
	 *
	 * 安全模式将对消息执行加密和签名
	 * 1. 对要发送的消息进行AES-CBC加密
	 * 2. 生成安全签名
	 * 3. 将消息密文和安全签名打包成xml格式
	 *
	 * @param array $reply 公众平台待回复用户的消息，php 数组
	 * @param string $timestamp 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param string $nonce 一次性随机串，可以自己生成，也可以用URL参数的nonce 当return返回0时有效
	 *
	 * @return string 可直接用于回复用户的 XML
	 */
	public function prepare($reply, $timestamp, $nonce);

	/**
	 * 接收消息
	 *
	 * 安全模式下进行消息验证和解密
	 * 1. 利用收到的密文生成安全签名，进行签名验证
	 * 2. 若验证通过，则提取xml中的加密消息
	 * 3. 对消息进行解密
	 *
	 * @param string $msgSignature 签名串，对应 URL 参数的 msg_signature
	 * @param string $timestamp 时间戳 对应 URL 参数的 timestamp
	 * @param string $nonce 随机串，对应 URL 参数的 nonce
	 * @param string $postData 密文，对应 POST 请求的 body
     *
	 * @return array 消息的 PHP 数组表示
	 */
	public function receive($msgSignature, $timestamp, $nonce, $postData);
}
