<?php

namespace RickSelby\LaravelRequestFieldTypes\Facades;

use Illuminate\Support\Facades\Facade;
use RickSelby\LaravelRequestFieldTypes\FieldTypes;

class FieldTypesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FieldTypes::class;
    }
}
