<?php

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use InteractsWithDatabase;
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \BjornVoesten\CipherSweet\CipherSweetServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set(
            'app.key',
            'base64:Hn0XYG6Inl5TLOKtd+M3+sf6nfwfMsT0iF9Zf4ww5K0='
        );

        $app['config']->set(
            'ciphersweet.key',
            '4e1c44f87b4cdf21808762970b356891db180a9dd9850e7baf2a79ff3ab8a2fc'
        );

        $app['config']->set(
            'ciphersweet.crypto',
            'modern',
        );
    }

    protected function assertDatabaseHasFor(string $model, array $data)
    {
        return $this->assertDatabaseHas(
            (new $model)->getTable(),
            $data
        );
    }

    protected function assertDatabaseMissingFor(string $model, array $data)
    {
        return $this->assertDatabaseMissing(
            (new $model)->getTable(),
            $data
        );
    }
}
