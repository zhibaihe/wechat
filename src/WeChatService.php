<?php

namespace Zhibaihe\WeChat;

use Exception;

class WeChatService implements WeChatServiceInterface
{
	protected $token;
	protected $encodingAesKey;
	protected $appId;
	protected $appSecret;
    protected $key;
    protected $mode;

	/**
	 * 构造函数
	 *
	 * @param  string $mode 消息加解密方式 naked | compatible | safe
	 * @param  string $token 公众平台上，开发者设置的 token
	 * @param  string $encodingAesKey 公众平台上，开发者设置的EncodingAESKey
	 * @param  string $appId 公众平台的 appId
	 * @param  string $appSecret 公众平台的 app_secret
	 *
	 * @throws WeChatException
	 */
	public function __construct($mode, $token, $encodingAesKey, $appId, $appSecret)
	{
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		$this->mode = $mode;

		if($this->mode == 'safe' && strlen($this->encodingAesKey) != 43) {
            throw new WeChatException('Illegal AES Key', WeChatException::$IllegalAesKey);
		}

		$this->key = base64_decode($encodingAesKey . "=");
	}

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
    public function validate($signature, $timestamp, $nonce)
    {
        $sign = $this->sign([$timestamp, $nonce]);

        return $sign == $signature;
    }

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
	public function prepare($reply, $timestamp, $nonce)
	{
        $replyMsg = $this->array2xml('xml', $reply);

        if($this->mode != 'safe')
        {
        	return $replyMsg;
        }

		//加密
        $encrypt = $this->encrypt($replyMsg);

		//生成安全签名
		$signature = $this->sign([$timestamp, $nonce, $encrypt]);

        return $this->array2xml('xml', [
            'Encrypt' => $encrypt,
            'MsgSignature' => $signature,
            'TimeStamp' => $timestamp,
            'Nonce' => $nonce
        ]);
	}

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
	public function receive($msgSignature, $timestamp, $nonce, $postData)
	{
		//提取密文
        $message = $this->xml2array($postData);

        if($this->mode != 'safe')
        {
        	return $message;
        }

        $signature = $this->sign([$timestamp, $nonce, $message['Encrypt']]);

		if ($signature != $msgSignature) {
            throw new WeChatException("Signature ($msgSignature) is invalid. Expected value: ($signature)", WeChatException::$ValidateSignatureError);
		}

        $decrypted = $this->decrypt($message['Encrypt']);

        return $this->xml2array($decrypted);
	}

    /**
     * 将 xmlstring 转化为PHP的关联数组表示
     *
     * @param string $xmlstring 待转换的 XML 字符串
     *
     * @return array 转换后的 PHP 数组
     */
    protected function xml2array($xmlstring)
    {
        try{
            $xml = simplexml_load_string($xmlstring, 'SimpleXMLElement', LIBXML_NOCDATA);
            $json = json_encode($xml);
        }catch(Exception $e){
            throw new WeChatException('ParseXMLError: '. $e->getMessage() . " : ($xmltext)" , WeChatException::$ParseXmlError);
        }

        return json_decode($json,TRUE);
    }

    /**
     * 将数组 $arr 转换为 XML 表示
     *
     * @param string $tag 根节点标签
     * @param string $arr 待转换的 PHP 数组
     *
     * @return string 转换后的 XML 字符串
     */
    protected function array2xml($tag, $arr)
    {
        $xml = "<$tag>";
        foreach($arr as $key => $value){
            if(is_array($value)){
                $xml .= "<$key>";
                foreach($value['items'] as $item){
                    $xml .= $this->array2xml($value['tag'], $item);
                }
                $xml .= "</$key>";
            }else{
                $value = is_numeric($value) ? $value : "<![CDATA[$value]]>";
                $xml .= sprintf("<%s>%s</%s>", $key, $value, $key);
            }
        }
        $xml .= "</$tag>";

        return $xml;
    }

    /**
     * 对 bundle (PHP 数组) 进行签名
     *
     * 1. 将 token 加入bundle数组
     * 2. 按字符串进行排序
     * 3. 把bundle中所有字符串首尾相连
     * 4. 计算并返回 sha1 哈希值
     *
     * @param array $bundle 待签名数组
     *
     * @return string 签名
     * 
     */
	protected function sign($bundle)
	{
        $bundle = array_merge($bundle, [ $this->token ]);

        sort($bundle, SORT_STRING);

        return sha1(implode($bundle));
	}

	/**
	 * 对明文进行加密
     *
	 * @param string $text 需要加密的明文
	 *
	 * @return string 加密后的密文
	 */
	protected function encrypt($text)
	{
		try {
			//获得16位随机字符串，填充到明文之前
			$random = str_random(16);
			$text = $random . pack("N", strlen($text)) . $text . $this->appId;
			// 网络字节序
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			//使用自定义的填充方式对明文进行补位填充
			$pkc_encoder = new PKCS7Encoder;
			$text = $pkc_encoder->encode($text);
			mcrypt_generic_init($module, $this->key, $iv);
			//加密
			$encrypted = mcrypt_generic($module, $text);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);

			//使用BASE64对加密后的字符串进行编码
            return base64_encode($encrypted);
		} catch (Exception $e) {
            throw new WeChatException($e->getMessage(), WeChatException::$EncryptAESError);
		}
	}

	/**
	 * 对密文进行解密
     *
	 * @param string $encrypted 需要解密的密文
	 *
	 * @return string 解密得到的明文
	 */
	protected function decrypt($encrypted)
	{
		try {
			//使用BASE64对需要解密的字符串进行解码
			$ciphertext_dec = base64_decode($encrypted);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			mcrypt_generic_init($module, $this->key, $iv);

			//解密
			$decrypted = mdecrypt_generic($module, $ciphertext_dec);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (Exception $e) {
            throw new WeChatException($e->getMessage(), WeChatException::$DecryptAESError);
		}

		try {
			//去除补位字符
			$pkc_encoder = new PKCS7Encoder;
			$result = $pkc_encoder->decode($decrypted);
			//去除16位随机字符串,网络字节序和AppId
			if (strlen($result) < 16)
				return "";
			$content = substr($result, 16, strlen($result));
			$len_list = unpack("N", substr($content, 0, 4));
			$xml_len = $len_list[1];
			$xml_content = substr($content, 4, $xml_len);
			$from_appid = substr($content, $xml_len + 4);
		} catch (Exception $e) {
            throw new WeChatException($e->getMessage(), WeChatException::$IllegalBuffer);
		}
		if ($from_appid != $this->appId){
            throw new WeChatException("Invalid app ID : $from_appid", WeChatException::$ValidateAppidError);
        }

		return $xml_content;
	}
}

