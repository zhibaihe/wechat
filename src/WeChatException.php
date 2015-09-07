<?php

namespace Zhibaihe\WeChat;

use Exception;

/**
 * 微信公众平台接口异常
 *
 * 提供不同异常状态的错误码和提示信息
 */
class WeChatException extends Exception
{
    public static $OK                     = 0;
    public static $ValidateSignatureError = -40001;
    public static $ParseXmlError          = -40002;
    public static $ComputeSignatureError  = -40003;
    public static $IllegalAesKey          = -40004;
    public static $ValidateAppidError     = -40005;
    public static $EncryptAESError        = -40006;
    public static $DecryptAESError        = -40007;
    public static $IllegalBuffer          = -40008;
    public static $EncodeBase64Error      = -40009;
    public static $DecodeBase64Error      = -40010;
    public static $GenReturnXmlError      = -40011;

    protected $messages = [
        0      => 'OK',
        -40001 => 'Signature validation failed',
        -40002 => 'Parse XML error',
        -40003 => 'Compute signature error',
        -40004 => 'Illegal AES Key',
        -40005 => 'App ID validation error',
        -40006 => 'AES encryption error',
        -40007 => 'AES decryption error',
        -40008 => 'Illegal buffer',
        -40009 => 'Base64 encoding error',
        -40010 => 'Base64 decoding error',
        -40011 => 'Cannot generate response XML',
    ];

    public function __construct($message = '', $code = 0, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);

        if(array_key_exists($code, $this->messages)){
            $this->message .= sprintf("(%d: %s)", $code, $this->messages[$code]);
        }
    }
}
