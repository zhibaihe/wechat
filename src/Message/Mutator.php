<?php

namespace Zhibaihe\WeChat\Message;

class Mutator
{
    public static $map = [
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
    ];


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
        $mutated = [];

        foreach ($arr as $k => $v) {
            if (array_key_exists($k, $map)) {
                $mutated[ $map[$k] ] = is_array($v) ? self::mutate($v, $map) : $v;
            }
        }

        return $mutated;
    }
}
