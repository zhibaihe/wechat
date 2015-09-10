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

interface ListenerContract
{
    public function receive(Message $message);
}
