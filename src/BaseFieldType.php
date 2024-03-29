<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\Interfaces\FieldTypeInterface;
use RickSelby\LaravelRequestFieldTypes\Traits\MessagesTrait;
use RickSelby\LaravelRequestFieldTypes\Traits\RulesTrait;

abstract class BaseFieldType implements FieldTypeInterface
{
    use MessagesTrait, RulesTrait;

    public function __construct()
    {
        $this->initialiseMessages();
        $this->initialiseRules();
    }

    /**
     * Allow both simple input field names (as array values)
     * and definitions of rules for an input field name (as fieldName => rules).
     *
     * @param  array  $inputFields
     * @return Collection
     */
    public function setInputFields(array $inputFields): Collection
    {
        $fieldNames = collect();
        foreach ($inputFields as $key => $value) {
            if (is_string($key)) {
                if (! is_array($value)) {
                    $value = [$value];
                }
                $this->setRules($key, array_merge($value, $this->rules()));
                $fieldNames->push($key);
                $this->setMessagesFor($key);
            } else {
                $this->setRules($value, $this->rules());
                $fieldNames->push($value);
                $this->setMessagesFor($value);
            }
        }

        return $fieldNames;
    }

    /**
     * The rules to apply to each input field for this type.
     *
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * Set custom messages for the given input field.
     *
     * @param  string  $inputField
     */
    abstract protected function setMessagesFor($inputField);

    /**
     * Map the mapFunction() across all inputs for this field.
     *
     * @param  array  $requestValues
     * @return array
     */
    public function modifyInputAfterValidation(array $requestValues): array
    {
        return $this->mapFields($requestValues, $this->getInputFieldNames(), [$this, 'mapAfterValidationFunction']);
    }

    /**
     * By default, don't alter the input values.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function mapAfterValidationFunction($value)
    {
        return $value;
    }

    /**
     * Get a list of input fields for this field.
     *
     * @return Collection
     */
    protected function getInputFieldNames()
    {
        return $this->rules->keys();
    }

    /**
     * Alter the request object for the given fields using the given callback.
     *
     * @param  mixed[]  $requestValues  Values from the request
     * @param  Collection|array  $fieldNameList  List of input field names to work on
     * @param  callable  $callback  Function to run on the value
     * @return mixed[]
     */
    final protected function mapFields($requestValues, $fieldNameList, $callback)
    {
        $mergeArray = [];
        foreach ($fieldNameList as $field) {
            if (strpos($field, '*') !== false) {
                $mergeArray = $this->mapAsteriskField($requestValues, $field, $callback, $mergeArray);
            } else {
                data_set($mergeArray, $field, $this->mapFieldsRecursive(data_get($requestValues, $field), $callback));
            }
        }

        return array_replace_recursive($requestValues, $mergeArray);
    }

    /**
     * Map a callback for a field that contains at least one asterisk. Maintain key values.
     *
     * @param  $requestValues
     * @param  $field
     * @param  $callback
     * @param  $mergeArray
     * @return mixed
     */
    final protected function mapAsteriskField($requestValues, $field, $callback, $mergeArray)
    {
        $asterisk = strpos($field, '*');
        $base = substr($field, 0, $asterisk - 1);

        if ($asterisk == strlen($field) - 1) {
            // Nothing after the asterisk
            $data = $this->mapFieldsRecursive(data_get($requestValues, $base), $callback);
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    data_set($mergeArray, str_replace('*', $k, $field), $v);
                }
            }
        } else {
            // Selecting a field beyond the asterisk

            // Rebuild the rest of the field, stripping off the leading '.'
            $rest = substr($field, $asterisk + 2);

            $data = data_get($requestValues, $base);
            if (is_array($data)) {
                foreach ($data as $key => $values) {
                    // Recurse through the 'sub-data'. Should catch any other asterisks, too.
                    data_set($mergeArray, $base.'.'.$key, $this->mapFields($values, [$rest], $callback));
                }
            }
        }

        return $mergeArray;
    }

    /**
     * Recursively map a callback to all items in an array.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @return array
     */
    private function mapFieldsRecursive($value, $callback)
    {
        if (is_array($value)) {
            foreach ($value as $key => $subVal) {
                $value[$key] = $this->mapFieldsRecursive($subVal, $callback);
            }

            return $value;
        } else {
            return $callback($value);
        }
    }
}
