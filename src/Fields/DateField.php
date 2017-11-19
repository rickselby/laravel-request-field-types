<?php

namespace App\Http\Requests\Fields;

use Carbon\Carbon;

trait DateField
{
    protected $dateFields = [];
    protected $dateFormat = 'Y-m-d';

    public function hookRulesDate()
    {
        foreach($this->dateFields AS $field) {
            $this->addRules($field, 'date_format:"'.$this->dateFormat.'"');
        }
    }

    public function hookAfterConvertDates()
    {
        $this->mapFields($this->dateFields, function($value) {
            return $value ? Carbon::createFromFormat($this->dateFormat, $value) : null;
        });
    }
}
