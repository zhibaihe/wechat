<?php

namespace Zhibaihe\WeChat;

use Illuminate\Support\ServiceProvider;

class WeChatServiceProvider extends ServiceProvider
{
    public function boot()
    {
        require __DIR__ . '/Http/routes.php';

        $this->publishes([
             __DIR__ . '/config' => config_path('zhibaihe/wechat')
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton('Zhibaihe\WeChat\WeChatServiceInterface', function(){
            $wechat = new WeChatService(
                config('zhibaihe.wechat.mode'),
                config('zhibaihe.wechat.token'),
                config('zhibaihe.wechat.AESKey'),
                config('zhibaihe.wechat.app_id'),
                config('zhibaihe.wechat.app_secret')
            );
            return $wechat;
        });

        $this->mergeConfigFrom(__DIR__ . '/config/main.php', 'zhibaihe.wechat');
    }

    public function provides()
    {
        return ['Zhibaihe\WeChat\WeChatServiceInterface'];
    }
}