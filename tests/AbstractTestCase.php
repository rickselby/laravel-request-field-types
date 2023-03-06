<?php

namespace RickSelby\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use RickSelby\LaravelRequestFieldTypes\RequestFieldTypesServiceProvider;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    protected static function getServiceProviderClass(): string
    {
        return RequestFieldTypesServiceProvider::class;
    }
}
