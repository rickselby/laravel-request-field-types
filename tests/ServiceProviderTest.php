<?php

namespace RickSelby\Tests;

use RickSelby\LaravelRequestFieldTypes\FieldTypes;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testAutoPresenterMapperIsInjectable()
    {
        $this->assertIsInjectable(FieldTypes::class);
    }
}
