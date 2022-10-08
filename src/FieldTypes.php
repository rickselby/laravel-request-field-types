<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\Interfaces\FieldTypeInterface;

class FieldTypes
{
    /** @var Collection */
    private $fieldTypes;

    /** @var Application */
    private $app;

    public function __construct(Container $app)
    {
        $this->fieldTypes = new Collection();
        $this->app = $app;
    }

    /**
     * Set input fields for a field type, given by its identifier.
     *
     * @param  string  $fieldType  Class name for field type
     * @param  array  $fieldNames
     *
     * @returns Collection
     *
     * @throws \Exception
     */
    public function setInputsFor($fieldType, array $fieldNames): Collection
    {
        return $this->getIdentifier($fieldType)->setInputFields($fieldNames);
    }

    /**
     * Get a field by its identifier.
     *
     * @param  string  $fieldType  Class name for field type
     * @return FieldTypeInterface
     *
     * @throws \Exception
     */
    protected function getIdentifier($fieldType)
    {
        if (! $this->fieldTypes->has($fieldType)) {
            $this->register($fieldType);
        }

        return $this->fieldTypes->get($fieldType);
    }

    /**
     * Register a field type.
     *
     * @param  string  $class
     *
     * @throws \Exception
     */
    private function register(string $class)
    {
        $fieldType = $this->app->make($class);
        if (! $fieldType instanceof FieldTypeInterface) {
            throw new \Exception('Registered field type must implement FieldTypeInterface');
        }

        $this->fieldTypes->put($class, $fieldType);
    }

    /**
     * Get a list of rules for all registered fields.
     *
     * @return Collection
     */
    public function getRules(): Collection
    {
        return $this->fieldTypes
            ->map->getRules()
            ->collapse();
    }

    public function getMessages(): Collection
    {
        return $this->fieldTypes
            ->map->getMessages()
            ->collapse();
    }

    /**
     * Take the request input values and allow each field to modify them as required.
     *
     * @param  array  $request
     * @return array
     */
    public function modifyInputAfterValidation($request)
    {
        return $this->fieldTypes
            ->reduce(function ($requestValues, FieldTypeInterface $fieldType) {
                return $fieldType->modifyInputAfterValidation($requestValues);
            }, $request);
    }
}
