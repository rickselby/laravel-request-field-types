<?php

namespace RickSelby\Tests;

use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\FieldTypeInterface;
use RickSelby\LaravelRequestFieldTypes\FieldTypes;
use RickSelby\Tests\Stubs\MappedStub;
use RickSelby\Tests\Stubs\UnmappedStub;
use RickSelby\Tests\Stubs\MappedStubPresenter;
use RickSelby\LaravelAutoPresenterMapper\AutoPresenterMapper;

class FieldTypesTest extends AbstractTestCase
{
    /** @var FieldTypes */
    private $fieldTypes;

    private $mocks;
    private $rules;
    private $input;

    public function testRules()
    {
        $this->fieldTypes->register('firstMock');
        $this->assertEquals($this->rules, $this->fieldTypes->getRules());
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

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->fieldTypes = $app->make(FieldTypes::class);

        $this->rules = new Collection(['rules']);
        $this->input = ['sth' => 'changed'];

        $this->makeFieldMocks(['firstMock', 'secondMock']);

        $app->singleton('firstMock', function() {
            return $this->mocks['firstMock'];
        });

        $app->singleton('secondMock', function() {
            return $this->mocks['secondMock'];
        });
    }

    private function makeFieldMocks(array $names)
    {
        foreach($names AS $name) {
            $this->mocks[$name] = $this->createMock(FieldTypeInterface::class);

            $this->mocks[$name]->method('getIdentifier')->willReturn($name);
            $this->mocks[$name]->method('setInputFields')->willReturn(null);
            $this->mocks[$name]->method('getRules')->willReturn($this->rules);
            $this->mocks[$name]->method('modifyInputAfterValidation')->willReturn($this->input);
        }
    }
}
