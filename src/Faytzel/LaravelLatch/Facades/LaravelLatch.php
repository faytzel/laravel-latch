<?php namespace Faytzel\LaravelLatch\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelLatch extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'latch';
    }

}