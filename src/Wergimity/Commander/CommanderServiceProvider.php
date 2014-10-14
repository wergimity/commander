<?php namespace Wergimity\Commander;

use Illuminate\Support\ServiceProvider;

class CommanderServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->app->singleton('commander', Commander::class);
    }

    public function provides()
    {
        return array('commander');
    }

}
