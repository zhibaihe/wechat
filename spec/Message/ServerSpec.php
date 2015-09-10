<?php

namespace spec\Zhibaihe\WeChat\Message;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zhibaihe\WeChat\Contracts\ListenerContract;
use Zhibaihe\WeChat\Contracts\ResponderContract;
use Zhibaihe\WeChat\Message\Message;
use Zhibaihe\WeChat\Message\Server;

/**
 * Class ServerSpec
 * @package spec\Zhibaihe\WeChat\Message
 *
 * @mixin Server
 */
class ServerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Zhibaihe\WeChat\Message\Server');
    }

    public function let()
    {
        $this->beConstructedWith('id', 'token', null);
    }

    public function it_registers_message_listeners_and_calls_them_properly(ListenerContract $listener1, ListenerContract $listener2)
    {
        $message = new Message(['type' => 'text']);

        $this->on('message.text')->tell([$listener1, 'receive'])->tell([$listener2, 'receive']);

        $listener1->receive($message)->shouldBeCalled();
        $listener2->receive($message)->shouldBeCalled();

        $this->broadcast($message);
    }

    public function it_registers_responders_and_calls_them_properly(ResponderContract $responder1, ResponderContract $responder2)
    {
        $text = new Message(['type' => 'text']);
        $image = new Message(['type' => 'image']);

        $this->on('message.text')->reply([$responder1, 'respond']);
        $this->on('message.image')->reply([$responder2, 'respond']);

        $responder1->respond($text)->willReturn(new Message);
        $responder2->respond($image)->willReturn(new Message);

        $this->respond($text);
        $this->respond($image);

        $responder1->respond($text)->shouldHaveBeenCalled();
        $responder2->respond($image)->shouldHaveBeenCalled();
    }
}
