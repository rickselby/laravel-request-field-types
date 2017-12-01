<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Support\Collection;

/**
 * Allow a class to manage a list of rules for input fields.
 */
trait RulesTrait
{
    /** @var Collection */
    protected $rules;

    public function setRules($inputField, array $rules)
    {
        $this->initialiseRules();
        if ($this->rules->has($inputField)) {
            $this->rules->put($inputField, array_unique(array_merge($this->rules->get($inputField), $rules)));
        } else {
            $this->rules->put($inputField, $rules);
        }
    }

    public function getRules(): Collection
    {
        $this->initialiseRules();

        return $this->rules;
    }

    private function initialiseRules()
    {
        if (! isset($this->rules)) {
            $this->rules = new Collection();
        }
    }
}
