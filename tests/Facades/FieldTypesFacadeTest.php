<?php

namespace RickSelby\Tests\Facades;

use RickSelby\Tests\AbstractTestCase;
use GrahamCampbell\TestBenchCore\FacadeTrait;
use RickSelby\LaravelRequestFieldTypes\FieldTypes;
use RickSelby\LaravelRequestFieldTypes\Facades\FieldTypesFacade;

class FieldTypesFacadeTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return FieldTypes::class;
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return FieldTypesFacade::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return FieldTypes::class;
    }
}
