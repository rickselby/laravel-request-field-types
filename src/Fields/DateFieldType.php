<?php

namespace RickSelby\LaravelRequestFieldTypes\Fields;

use Carbon\Carbon;
use RickSelby\LaravelRequestFieldTypes\BaseFieldType;

class DateFieldType extends BaseFieldType
{
    protected $identifier = 'date';

    protected $dateFormat = 'Y-m-d';

    protected function rules(): array
    {
        return [
            'date_format:"'.$this->dateFormat.'"',
        ];
    }

    /**
     * Convert the dates to carbon instances.
     *
     * @param mixed $value
     *
     * @return null|Carbon
     */
    protected function mapAfterValidationFunction($value)
    {
        return $value ? Carbon::createFromFormat($this->dateFormat, $value)->startOfDay() : null;
    }
}
