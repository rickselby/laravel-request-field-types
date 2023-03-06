<?php

namespace RickSelby\Tests;

use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\Traits\MessagesTrait;

class MessagesTraitTest extends AbstractTestCase
{
    public function testGetMessagesIsCollection()
    {
        $this->assertInstanceOf(Collection::class, $this->messagesTrait->getMessages());
    }

    public function testSetMessages()
    {
        $this->messagesTrait->setMessage('field', 'rule');

        $messages = $this->messagesTrait->getMessages();

        $this->assertEquals(1, $messages->count());
        $this->assertEquals('field', $messages->keys()->first());
        $this->assertEquals('rule', $messages->first());
    }

    public function testSetMultipleMessages()
    {
        $this->messagesTrait->setMessage('field', 'rule1');
        $this->messagesTrait->setMessage('field', 'rule2');

        $messages = $this->messagesTrait->getMessages();

        $this->assertEquals(1, $messages->count());
        $this->assertEquals('field', $messages->keys()->first());
        $this->assertEquals('rule2', $messages->first());
    }

    /***************************************************************************************************/

    /** @var MessagesTrait */
    private $messagesTrait;

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $this->messagesTrait = $this->getMockForTrait(MessagesTrait::class);
    }
}
