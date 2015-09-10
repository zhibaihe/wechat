<?php

/*
 * This file is part of the non-official WeChat SDK developed by Zhiyan.
 *
 * (c) DUAN Zhiyan <zhiyan@zhibaihe.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
    public function respondTo();

    public function respond(Message $message);
}
