<?php

namespace Cpp\Parse;

use Cpp\Parse\SessionStorage;
use Illuminate\Support\Facades\Validator;
use Parse\HttpClients\ParseCurlHttpClient;
use Parse\ParseClient;
use Illuminate\Support\Facades\Auth;
use Cpp\Parse\ParseUserProvider;
use Illuminate\Support\ServiceProvider;
use Cpp\Parse\Console\ModelMakeCommand;
use Cpp\Parse\Auth\Providers\UserProvider;
use Laravel\Lumen\Application as LumenApplication;
use Cpp\Parse\Auth\Providers\AnyUserProvider;
use Cpp\Parse\Auth\Providers\FacebookUserProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Cpp\Parse\Auth\SessionGuard;
use Parse\ParseException;
use Parse\ParseUser;

class ParseServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();

        $this->registerCommands();

        $this->setupParse();

        $this->setupValidator();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../config/parse.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('parse.php')], 'parse');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('parse');
        }

        $this->mergeConfigFrom($source, 'parse');
    }

    protected function registerCommands()
    {
        $this->registerModelMakeCommand();

        $this->commands('command.parse.model.make');
    }

    protected function registerModelMakeCommand()
    {
        $this->app->singleton('command.parse.model.make', function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Setup parse.
     *
     * @return void
     */
    protected function setupParse()
    {
        $config = $this->app->config->get('parse');

        try {
            ParseClient::setStorage(new SessionStorage());
            ParseClient::initialize($config['app_id'], $config['rest_key'], $config['master_key']);
            ParseClient::setServerURL($config['server_url'], $config['mount_path']);
            ParseClient::setHttpClient(new ParseCurlHttpClient());
        } catch(ParseException $e) {
            dd($e);
        }

        // Register providers
        Auth::provider('parse', function($app, array $config) {
            return new UserProvider($config['model']);
        });

        Auth::provider('parse-facebook', function($app, array $config) {
            return new FacebookUserProvider($config['model']);
        });

        Auth::provider('parse-any', function($app, array $config) {
            return new AnyUserProvider($config['model']);
        });

        // Register guard
        Auth::extend('session-parse', function($app, $name, array $config) {
            $guard = new SessionGuard($name, Auth::createUserProvider($config['provider']), $app['session.store']);

            $guard->setCookieJar($this->app['cookie']);
            $guard->setDispatcher($this->app['events']);
            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));

            return $guard;
        });
    }

    protected function setupValidator() {
        /**
         * Cette validation permet de verifier si l'utilisateur est déja enregistrer
         */
        Validator::extend('unique_parse_user', function ($attribute, $value) {
            $query = ParseUser::query();
            $result = $query->equalTo('username', $value)->first(true);
            return !(bool)$result;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            //
        ];
    }
}
