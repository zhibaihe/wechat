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

class Factory
{
    public static function create($attributes)
    {
        return new Message($attributes);
    }

    public static function make($race)
    {
        list($catgory, $type) = explode('.', $race);

        return new Message(self::$recipe[$race]);
    }

    protected static $recipe = array(
        'message.text' => array('type' => 'text', 'content' => ''),
        'message.image' => array('type' => 'image', 'image' => ''),
    );
}
