<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Support\Collection;

abstract class BaseFieldType implements FieldTypeInterface
{
    use RulesTrait;

    /** @var string */
    protected $identifier;

    /**
     * Get the identifier for the field for registration
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Allow both simple input field names (as array values)
     * and definitions of rules for an input field name (as fieldName => rules)
     *
     * @param array $inputFields
     */
    public function setInputFields(array $inputFields)
    {
        foreach($inputFields AS $key => $value) {
            if (is_string($key)) {
                $this->setRules($key, array_merge($value, $this->rules()));
            } else {
                $this->setRules($value, $this->rules());
            }
        }
    }

    /**
     * The rules to apply to each input field for this type
     *
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * Map the mapFunction() across all inputs for this field
     *
     * @param array $requestValues
     *
     * @return array
     */
    public function modifyInputAfterValidation(array $requestValues): array
    {
        return $this->mapFields($requestValues, $this->getInputFieldNames(), [$this, 'mapAfterValidationFunction']);
    }

    /**
     * By default, don't alter the input values
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function mapAfterValidationFunction($value)
    {
        return $value;
    }

    /**
     * Get a list of input fields for this field
     *
     * @return Collection
     */
    protected function getInputFieldNames()
    {
        return $this->rules->keys();
    }

    /**
     * Alter the request object for the given fields using the given callback
     *
     * @param mixed[] $requestValues Values from the request
     * @param Collection $fieldNameList List of input field names to work on
     * @param callback $callback  Function to run on the value
     *
     * @return mixed[]
     */
    final protected function mapFields($requestValues, $fieldNameList, $callback)
    {
        $mergeArray = [];
        foreach($fieldNameList AS $field) {
            if (strstr($field, '*')) {
                // If we're replacing multiple things with an asterisk, we need
                // to fiddle it...
                $data = $this->mapFieldsRecursive(data_get($requestValues, $field), $callback);
                if (is_array($data)) {
                    foreach ($data AS $k => $v) {
                        data_set($mergeArray, str_replace('*', $k, $field), $v);
                    }
                }
            } else {
                data_set($mergeArray, $field, $this->mapFieldsRecursive(data_get($requestValues, $field), $callback));
            }
        }
        return array_replace_recursive($requestValues, $mergeArray);
    }

    /**
     * Recursively map a callback to all items in an array
     *
     * @param mixed $value
     * @param Callable $callback
     *
     * @return array
     */
    final private function mapFieldsRecursive($value, $callback)
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
}
