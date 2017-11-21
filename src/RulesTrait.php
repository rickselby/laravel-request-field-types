<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Support\Collection;

/**
 * Allow a class to manage a list of rules for input fields
 *
 * @package RickSelby\LaravelRequestFieldTypes
 */
trait RulesTrait
{
    /** @var Collection */
    protected $rules;

    public function setRule($field, $rules)
    {
        $this->initialiseRules();
        $this->rules->put($field, $rules);
    }

    public function getRules(): Collection
    {
        $this->initialiseRules();
        return $this->rules;
    }

    private function initialiseRules()
    {
        if (!isset($this->rules)) {
            $this->rules = new Collection();
        }
    }
}