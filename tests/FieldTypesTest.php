<?php

namespace RickSelby\Tests;

use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\FieldTypes;
use RickSelby\LaravelRequestFieldTypes\Interfaces\FieldTypeInterface;

class FieldTypesTest extends AbstractTestCase
{
    /**
     * @expectedException \Exception
     */
    public function testRegisteringWrongClassThrowsException()
    {
        $this->fieldTypes->register(self::class);
    }

    public function testRules()
    {
        $this->fieldTypes->register('firstMock');
        $this->assertEquals($this->rules, $this->fieldTypes->getRules());
    }

    public function testMessages()
    {
        $this->fieldTypes->register('firstMock');
        $this->assertEquals($this->messages, $this->fieldTypes->getMessages());
    }

    public function testRegisteringSameFieldTwiceDoesntDuplicate()
    {
        $this->fieldTypes->register('firstMock');
        $this->fieldTypes->register('firstMock');
        $this->assertEquals($this->rules, $this->fieldTypes->getRules());
    }

    public function testRegisteringTwoFieldsGetsBoth()
    {
        $this->fieldTypes->register('firstMock');
        $this->fieldTypes->register('secondMock');
        $this->assertEquals(
            $this->rules->concat($this->rules),
            $this->fieldTypes->getRules()
        );
    }

    public function testRegisteringTwoFieldsGetsBothMessages()
    {
        $this->fieldTypes->register('firstMock');
        $this->fieldTypes->register('secondMock');
        $this->assertEquals(
            $this->messages->concat($this->messages),
            $this->fieldTypes->getMessages()
        );
    }

    public function testInputModify()
    {
        $this->fieldTypes->register('firstMock');
        $this->assertEquals($this->input, $this->fieldTypes->modifyInputAfterValidation([]));
    }

    public function testInputModifyChains()
    {
        $this->mocks['firstMock']
            ->expects($this->once())
            ->method('modifyInputAfterValidation')
            ->with([]);
        $this->mocks['secondMock']
            ->expects($this->once())
            ->method('modifyInputAfterValidation')
            ->with($this->input);
        $this->fieldTypes->register('firstMock');
        $this->fieldTypes->register('secondMock');
        $this->fieldTypes->modifyInputAfterValidation([]);
    }

    public function testSetInputsCalls()
    {
        $this->mocks['firstMock']->expects($this->once())->method('setInputFields');
        $this->fieldTypes->register('firstMock');
        $this->fieldTypes->setInputsFor('firstMock', []);
    }

    public function testSetInputsCallsCorrect()
    {
        $this->mocks['firstMock']->expects($this->once())->method('setInputFields');
        $this->mocks['secondMock']->expects($this->never())->method('setInputFields');
        $this->fieldTypes->register('firstMock');
        $this->fieldTypes->register('secondMock');
        $this->fieldTypes->setInputsFor('firstMock', []);
    }

    /**
     * @expectedException \Exception
     */
    public function testUnknownFieldThrowsException()
    {
        // don't register anything
        $this->fieldTypes->setInputsFor('firstMock', []);
    }

    /***************************************************************************************************/

    /** @var FieldTypes */
    private $fieldTypes;

    private $mocks;
    private $rules;
    private $input;
    private $messages;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->fieldTypes = $app->make(FieldTypes::class);

        $this->rules = new Collection(['rules']);
        $this->messages = new Collection(['message']);
        $this->input = ['sth' => 'changed'];

        $this->makeFieldMocks(['firstMock', 'secondMock']);

        $app->singleton('firstMock', function () {
            return $this->mocks['firstMock'];
        });

        $app->singleton('secondMock', function () {
            return $this->mocks['secondMock'];
        });
    }

    private function makeFieldMocks(array $names)
    {
        foreach ($names as $name) {
            $this->mocks[$name] = $this->createMock(FieldTypeInterface::class);

            $this->mocks[$name]->method('getIdentifier')->willReturn($name);
            $this->mocks[$name]->method('setInputFields')->willReturn(collect());
            $this->mocks[$name]->method('getRules')->willReturn($this->rules);
            $this->mocks[$name]->method('getMessages')->willReturn($this->messages);
            $this->mocks[$name]->method('modifyInputAfterValidation')->willReturn($this->input);
        }
    }
}
