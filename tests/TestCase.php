<?php

namespace Felix\Metadata\Tests;

use Felix\Metadata\MetaServiceProvider;
use Orchestra\Testbench\TestCase as Base;

class TestCase extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'tests');
        $app['config']->set('database.connections.tests', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [MetaServiceProvider::class];
    }
}
