<?php

namespace RickSelby\Tests\Stubs;

use RickSelby\LaravelRequestFieldTypes\BaseFieldType;

abstract class TouchedFieldTypeStub extends BaseFieldType
{
    public function mapAfterValidationFunction($value)
    {
        return 'touched';
    }
}