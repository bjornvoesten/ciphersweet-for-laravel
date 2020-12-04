<?php

namespace BjornVoesten\CipherSweet;

use BjornVoesten\CipherSweet\Console\Commands\KeyGenerate;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class CipherSweetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws \Exception
     */
    public function register()
    {
        // Container
        $this->app->singleton(
            'ciphersweet',
            CipherSweetService::class,
        );

        // Commands
        $this->commands(KeyGenerate::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        // Config
        $this->publishes([
            __DIR__ . '/../config/ciphersweet.php' => config_path('ciphersweet.php')
        ], 'ciphersweet-config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/ciphersweet.php',
            'ciphersweet-config'
        );

        // Blueprint macros
        Blueprint::mixin(new Macros\Blueprint());
    }
}
