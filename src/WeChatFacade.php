<?php

namespace Zhibaihe\WeChat;

use Illuminate\Support\Facades\Facade;

class DemoFacade extends Facade
{
    protected static function getFacadeAccessor()
    { 
        return 'Zhibaihe\WeChat\Server';
    }
}