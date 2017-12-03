<?php

namespace RickSelby\LaravelRequestFieldTypes;

use Illuminate\Support\Collection;

/**
 * Allow a class to manage a list of messages for input fields.
 */
trait MessagesTrait
{
    /** @var Collection */
    protected $messages;

    public function setMessage($rule, $message)
    {
        $this->initialiseMessages();
        $this->messages->put($rule, $message);
    }

    public function getMessages(): Collection
    {
        $this->initialiseMessages();

        return $this->messages;
    }

    private function initialiseMessages()
    {
        if (! isset($this->messages)) {
            $this->messages = collect();
        }
    }
}
