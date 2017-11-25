<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Support\Collection;

interface FieldTypeInterface
{
    /**
     * Get the identifier for this field
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Set a list of input fields
     *
     * @param array $inputFields
     */
    public function setInputFields(array $inputFields);

    /**
     * Get a list of rules for input fields
     *
     * @return Collection
     */
    public function getRules(): Collection;

    /**
     * Take the list of input values and modify them as required
     *
     * @param array $requestValues
     *
     * @return array
     */
    public function modifyInputAfterValidation(array $requestValues): array;
}