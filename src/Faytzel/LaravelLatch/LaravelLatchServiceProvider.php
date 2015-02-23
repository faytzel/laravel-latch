<?php namespace Faytzel\LaravelLatch;

use Illuminate\Support\ServiceProvider;
use Faytzel\LaravelLatch\Api\Latch;

class LaravelLatchServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // load langs
        $this->loadTranslationsFrom(__DIR__.'/../../lang/', 'laravel-latch');

        // load configs
        $this->publishes([
            __DIR__.'/../../config/latch.php' => config_path('latch.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['latch'] = $this->app->share(function($app)
        {
            $config = $this->app['config']->get('latch');

            return new LaravelLatch(
                new Latch($config['app_id'], $config['app_secret']),
                $config
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('latch');
    }

}
