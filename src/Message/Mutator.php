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

class Mutator
{
    public static $map = array(
        'ToUserName'       => 'to',
        'FromUserName'     => 'from',
        'CreateTime'       => 'timestamp',
        'MsgType'          => 'type',
        'Event'            => 'event',
        'Content'          => 'content',
        'MsgId'            => 'id',
        'MediaId'          => 'media',
        'ThumbnailMediaId' => 'thumb',
        'Format'           => 'format',
        'PicUrl'           => 'picture',
        'Url'              => 'url',
        'Location_X'       => 'latitude',
        'Location_Y'       => 'longitude',
        'Scale'            => 'scale',
        'Label'            => 'label',
        'Title'            => 'title',
        'Description'      => 'description',
        'Music'            => 'music',
        'Image'            => 'image',
    );


    public static function prettify($arr)
    {
        return self::mutate($arr, self::$map);
    }

    public static function uglify($arr)
    {
        return self::mutate($arr, array_flip(self::$map));
    }

    protected static function mutate($arr, $map)
    {
        $mutated = array();

        foreach ($arr as $k => $v) {
            if (array_key_exists($k, $map)) {
                $mutated[ $map[$k] ] = is_array($v) ? self::mutate($v, $map) : $v;
            }
        }

        return $mutated;
    }
}
