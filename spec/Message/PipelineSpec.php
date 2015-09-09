<?php

namespace spec\Zhibaihe\WeChat\Message;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PipelineSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Zhibaihe\WeChat\Message\Pipeline');
    }
}
