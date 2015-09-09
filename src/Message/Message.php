<?php

namespace Zhibaihe\WeChat\Message;

class Message
{
    protected $from;

    protected $to;

    protected $timestamp;

    protected $attributes = [];

    public function __construct($attrs = [])
    {
        foreach ($attrs as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            } else {
                $this->attributes[$k] = $v;
            }
        }
    }

    public function toArray()
    {
        return array_merge($this->attributes, [
            'to' => $this->to,
            'from' => $this->from,
            'timestamp' => $this->timestamp
        ]);
    }

    public function fill($attrs)
    {
        foreach ($attrs as $k => $v) {
            $this->$k = $v;
        }
    }

    public function race()
    {
        if (in_array($this->type, ['text', 'image', 'audio', 'video', 'shortvideo', 'link', 'location'])) {
            return "message.$this->type";
        } elseif ($this->type == 'event') {
            return "event.{$this->event}";
        }

        return $this->type;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if (! array_key_exists($name, $this->attributes)) {
            return null;
        }

        return $this->attributes[$name];
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }
}
