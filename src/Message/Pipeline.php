<?php

/*
 * This file is part of the non-official WeChat SDK developed by Zhiyan.
 *
 * (c) DUAN Zhiyan <zhiyan@zhibaihe.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Zhibaihe\WeChat\Message;

use Closure;
use Zhibaihe\WeChat\Exceptions\InvalidArgumentException;

/**
 * 消息处理流程
 *
 * 可通过此类别将多个处理逻辑组织成流水线
 * 消息将逐个经过流水线上的每一个模块
 */
class Pipeline
{
    protected $lines;

    public function __construct($configuration = array())
    {
        $this->lines = $configuration;
    }

    public function attach($type, $callable)
    {
        $callable = $this->parseCallable($callable);

        if (! array_key_exists($type, $this->lines)) {
            $this->lines[$type] = array();
        }

        $this->lines[$type][] = $callable;
    }

    public function detach($type, $callable)
    {
        if (! array_key_exists($type, $this->lines)) {
            return;
        }

        $this->lines[$type] = array_filter($this->lines, function ($line) use ($callable) {
            return $line === $callable;
        });
    }

    public function flush($type)
    {
        if (! array_key_exists($type, $this->lines)) {
            return;
        }

        unset($this->lines[$type]);
    }

    public function process($message)
    {
        $reply = new Message;

        $reply->from      = $message->to;
        $reply->to        = $message->from;
        $reply->timestamp = time();

        $race = $message->race();

        if (! array_key_exists($race, $this->lines)) {
            return $reply;
        }

        foreach ($this->lines[$race] as $callback) {
            call_user_func_array($callback, array($message, $reply));
        }

        return $reply;
    }

    protected function parseCallable($callback)
    {
        if (is_object($callback) && ($callback instanceof Closure)) {
            return $callback;
        }

        $callback = ! is_string($callback) ?: explode('@', $callback);

        if (! is_callable($callback)) {
            throw new InvalidArgumentException("Invalid callback {$callback}");
        }

        return $callback;
    }
}
