<?php

namespace RickSelby\Tests;

use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use RickSelby\LaravelRequestFieldTypes\FieldTypes;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testAutoPresenterMapperIsInjectable()
    {
        $this->assertIsInjectable(FieldTypes::class);
    }
}
