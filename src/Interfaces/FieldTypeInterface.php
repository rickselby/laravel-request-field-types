<?php

namespace RickSelby\LaravelRequestFieldTypes\Interfaces;

use Illuminate\Support\Collection;

interface FieldTypeInterface
{
    /**
     * Set a list of input fields.
     *
     * @param  array  $inputFields
     * @return Collection List of input fields added
     */
    public function setInputFields(array $inputFields): Collection;

    /**
     * Get a list of rules for input fields.
     *
     * @return Collection
     */
    public function getRules(): Collection;

    /**
     * Get a list of messages for input fields.
     *
     * @return Collection
     */
    public function getMessages(): Collection;

    /**
     * Take the list of input values and modify them as required.
     *
     * @param  array  $requestValues
     * @return array
     */
    public function modifyInputAfterValidation(array $requestValues): array;
}
