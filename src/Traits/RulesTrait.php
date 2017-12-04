<?php

namespace RickSelby\LaravelRequestFieldTypes\Traits;

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
        $rules = collect($rules);
        $this->initialiseRules();
        if ($this->rules->has($inputField)) {
            $this->rules->put(
                $inputField,
                $this->rules->get($inputField)->merge($rules)->unique()->values()
            );
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
            $this->rules = collect();
        }
    }
}
