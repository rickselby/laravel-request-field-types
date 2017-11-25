<?php

namespace RickSelby\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use RickSelby\LaravelRequestFieldTypes\RequestFieldTypesServiceProvider;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return RequestFieldTypesServiceProvider::class;
    }
}
