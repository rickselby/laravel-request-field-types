<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Foundation\Http\FormRequest;

/**
 * An extended requests that allows the use of the Fields class to manage defined fields
 *
 * Class RequestFieldsRequest
 * @package RickSelby\LaravelRequestFieldTypes
 */
abstract class FieldTypesRequest extends FormRequest
{
    use RulesTrait;

    /** @var FieldTypes */
    protected $fields;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [],
                                array $files = [], array $server = [], $content = null, FieldTypes $fields)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->fields = $fields;
    }

    /**
     * Override validation, to modify the request data after successful validation
     */
    public function validate()
    {
        $this->defineRules();
        parent::validate();
        $this->runAfterValidate();
    }

    /**
     * Define your rules here.
     */
    abstract function defineRules();

    /**
     * Get all rules, defined in the fields and locally
     *
     * @return array
     */
    public function rules()
    {
        return $this->fields->getRules()
            ->union($this->getRules())
            ->map(function ($rules) {
                return implode('|', $rules);
            })
            ->toArray();
    }

    /**
     * Replace the input values with modified values from the defined fields
     */
    protected function runAfterValidate()
    {
        $this->replace($this->fields->modifyInputAfterValidation($this->all()));
    }
}
