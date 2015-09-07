<?php

namespace Zhibaihe\WeChat;

interface WeChatServiceInterface
{
    /**
     * 对外部请求进行签名验证，以确定该请求是否来自微信服务器
     *
     * $signature string
     * $timestamp string
     * $nonce string
     *
     * 以上三者均来自外部请求
     */
    public function validateSignature($signature, $timestamp, $nonce);

	/**
	 * 将公众平台回复用户的消息加密打包.
	 * <ol>
	 *    <li>对要发送的消息进行AES-CBC加密</li>
	 *    <li>生成安全签名</li>
	 *    <li>将消息密文和安全签名打包成xml格式</li>
	 * </ol>
	 *
	 * @param $reply string 公众平台待回复用户的消息，php 数组
	 * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
	 *                      当return返回0时有效
	 *
	 * @return string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
	 */
	public function encryptMessage($reply, $timeStamp, $nonce);

	/**
	 * 检验消息的真实性，并且获取解密后的明文.
	 * <ol>
	 *    <li>利用收到的密文生成安全签名，进行签名验证</li>
	 *    <li>若验证通过，则提取xml中的加密消息</li>
	 *    <li>对消息进行解密</li>
	 * </ol>
	 *
	 * @param $msgSignature string 签名串，对应URL参数的msg_signature
	 * @param $timestamp string 时间戳 对应URL参数的timestamp
	 * @param $nonce string 随机串，对应URL参数的nonce
	 * @param $postData string 密文，对应POST请求的数据
     *
	 * @return string 解密后的原文(已将xml转化为PHP数组)
	 */
	public function decryptMessage($msgSignature, $timestamp, $nonce, $postData);
}
