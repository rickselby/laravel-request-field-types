<?php

namespace RickSelby\Tests\Stubs;

use RickSelby\LaravelRequestFieldTypes\FieldTypesRequest;

abstract class AuthorizedRequestStub extends FieldTypesRequest
{
    public function authorize()
    {
        return true;
    }
}
