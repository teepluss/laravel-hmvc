<?php namespace Teepluss\Hmvc;

use Guzzle\Http\Client;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class HmvcServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap classes for packages.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('teepluss/hmvc');

        // Auto create app alias with boot method.
        $loader = AliasLoader::getInstance()->alias('HMVC', 'Teepluss\Hmvc\Facades\HMVC');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register providers.
        $this->registerHmvc();

        // Register commands.
        $this->registerHmvcCallCommand();

        // Assign commands.
        $this->commands(
            'hmvc.call'
        );
    }

    /**
     * Register Api.
     *
     * @return void
     */
    public function registerHmvc()
    {
        $this->app['hmvc'] = $this->app->share(function($app)
        {
            $remoteClient = new Client();

            return new Hmvc($app['config'], $app['router'], $app['request'], $remoteClient);
        });
    }

    /**
     * Register Api Call command.
     *
     * @return void
     */
    public function registerHmvcCallCommand()
    {
        $this->app['hmvc.call'] = $this->app->share(function($app)
        {
            return new Commands\HmvcCallCommand($app['hmvc']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('hmvc');
    }

}