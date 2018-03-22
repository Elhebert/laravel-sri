<?php

namespace Elhebert\SubresourceIntegrity\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Elhebert\SubresourceIntegrity\SriServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            SriServiceProvider::class,
        ];
    }
}
