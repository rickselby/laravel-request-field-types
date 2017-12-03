<?php

namespace RickSelby\LaravelRequestFieldTypes\Fields;

use Carbon\Carbon;
use RickSelby\LaravelRequestFieldTypes\BaseFieldType;

class DateFieldType extends BaseFieldType
{
    const ID = 'date';

    protected $dateFormat = 'Y-m-d';

    protected function rules(): array
    {
        return [
            'date_format:"'.$this->dateFormat.'"',
        ];
    }

    protected function mapAfterValidationFunction($value)
    {
        return $value ? Carbon::createFromFormat($this->dateFormat, $value)->startOfDay() : null;
    }

    protected function setMessagesFor($inputField)
    {
        // No custom rules
    }
}
