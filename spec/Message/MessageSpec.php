<?php

namespace spec\Zhibaihe\WeChat\Message;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zhibaihe\WeChat\Message\Message;

/**
 * @mixin Message;
 */
class MessageSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->beConstructedWith([
            'from' => 'Zhiyan',
            'to' => 'DUAN',
            'timestamp' => '2015',
        ]);
        $this->shouldHaveType('Zhibaihe\WeChat\Message\Message');
    }

    public function it_can_be_filled_with_an_array()
    {
        $this->fill([
            'from' => 'some guy',
            'type' => 'text',
            'content' => 'hello',
        ]);

        $this->from->shouldBe('some guy');
        $this->type->shouldBe('text');
        $this->content->shouldBe('hello');
    }

    public function it_emits_null_for_undefined_attribute()
    {
        $this->dummy->shouldBe(null);
    }
}
