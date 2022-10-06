<?php

namespace RickSelby\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use RickSelby\LaravelRequestFieldTypes\RequestFieldTypesServiceProvider;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    protected function getServiceProviderClass()
    {
        return RequestFieldTypesServiceProvider::class;
    }
}
