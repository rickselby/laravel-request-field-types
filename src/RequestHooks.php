<?php

namespace RickSelby\LaravelRequestHooks;

use Illuminate\Foundation\Http\FormRequest;

abstract class RequestHooks extends FormRequest
{
    private $rules = [];

    /**
     * Add rules for a field
     *
     * @param $field
     * @param array|string $rules
     *
     * @return $this
     */
    protected function addRules($field, $rules)
    {
        if (!isset($rules[$field])) {
            $this->rules[$field] = [];
        }

        if (is_array($rules)) {
            $this->rules[$field] = array_merge($this->rules[$field], $rules);
        } else {
            $this->rules[$field][] = $rules;
        }

        return $this;
    }

    /**
     * Get the (correctly formatted) list of rules
     *
     * @return array
     */
    protected function getRules()
    {
        $formatted = [];
        foreach($this->rules AS $field => $rules) {
            $formatted[$field] = implode('|', $rules);
        }
        return $formatted;
    }

    /**
     * Default rules - get all the defined rules
     *
     * @return array
     */
    public function rules()
    {
        return $this->getRules();
    }

    public function validate()
    {
        $this->addFieldRules();
        $this->runBeforeValidate();
        parent::validate();
        $this->runAfterValidate();
    }

    private function addFieldRules()
    {
        foreach(preg_grep('/^hookRules/', get_class_methods($this)) AS $fieldRuleFunction) {
            call_user_func([$this, $fieldRuleFunction]);
        }
    }

    private function runBeforeValidate()
    {
        foreach(preg_grep('/^hookBefore/', get_class_methods($this)) AS $fieldRuleFunction) {
            call_user_func([$this, $fieldRuleFunction]);
        }
    }

    private function runAfterValidate()
    {
        foreach(preg_grep('/^hookAfter/', get_class_methods($this)) AS $fieldRuleFunction) {
            call_user_func([$this, $fieldRuleFunction]);
        }
    }

    /**
     * Alter the request object for the given fields using the given callback
     *
     * @param string[] $fieldList List of fields to work on
     * @param callback $callback  Function to run on the value
     */
    protected function mapFields($fieldList, $callback)
    {
        $mergeArray = [];
        foreach($fieldList AS $field) {
            if (strstr($field, '*')) {
                // If we're replacing multiple things with an asterisk, we need
                // to fiddle it...
                $data = $this->mapFieldsRecursive($this->input($field), $callback);
                if (is_array($data)) {
                    foreach ($data AS $k => $v) {
                        data_set($mergeArray, str_replace('*', $k, $field), $v);
                    }
                }
            } else {
                data_set($mergeArray, $field, $this->mapFieldsRecursive($this->input($field), $callback));
            }
        }
        $this->mergeRecursive($mergeArray);
    }

    /**
     * Recursively map a callback to all items in an array
     *
     * @param mixed $value
     * @param Callable $callback
     *
     * @return array
     */
    private function mapFieldsRecursive($value, $callback)
    {
        if (is_array($value)) {
            foreach($value AS $key => $subVal) {
                $value[$key] = $this->mapFieldsRecursive($subVal, $callback);
            }
            return $value;
        } else {
            return $callback($value);
        }
    }

    /**
     * $this->merge uses array_replace, and we need array_replace_recursive
     *
     * @param $array
     */
    private function mergeRecursive($array)
    {
        $this->replace(array_replace_recursive($this->all(), $array));
    }
}
