<?php

namespace spec\Zhibaihe\WeChat\Message;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MutatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Zhibaihe\WeChat\Message\Mutator');
    }

    function it_uglifies_news_message()
    {
        $news = array(
            'type' => 'news',
            'article-count' => 3,
            'articles' => array(
                'tag' => 'item',
                'items' => array(
                    array('title' => 'a', 'description' => 'b', 'picture' => 'c', 'url' => 'd'),
                    array('title' => 'A', 'description' => 'B', 'picture' => 'C', 'url' => 'D'),
                    array('title' => '1', 'description' => '2', 'picture' => '3', 'url' => '4'),
                )
            )
        );

        $expected = array(
            'MsgType' => 'news',
            'ArticleCount' => 3,
            'Articles' => array(
                'tag' => 'item',
                'items' => array(
                    array('Title' => 'a', 'Description' => 'b', 'PicUrl' => 'c', 'Url' => 'd'),
                    array('Title' => 'A', 'Description' => 'B', 'PicUrl' => 'C', 'Url' => 'D'),
                    array('Title' => '1', 'Description' => '2', 'PicUrl' => '3', 'Url' => '4'),
                )
            )
        );

        $this->uglify($news)->shouldBeLike($expected);
    }
}
