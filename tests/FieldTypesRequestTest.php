<?php

namespace RickSelby\Tests;

use RickSelby\LaravelRequestFieldTypes\FieldTypes;
use RickSelby\LaravelRequestFieldTypes\FieldTypesRequest;
use RickSelby\Tests\Stubs\AuthorizedRequestStub;

class FieldTypesRequestTest extends AbstractTestCase
{
    public function testRulesFormat()
    {
        $this->request->setRules('field', ['rule1', 'rule2']);
        $this->assertEquals(['field' => ['rule1', 'rule2']], $this->request->rules());
    }

    public function testMessagesFormat()
    {
        $this->request->setMessage('field', 'message');
        $this->assertEquals(['field' => 'message'], $this->request->messages());
    }

    public function testValidateResolved()
    {
        $this->request->expects($this->once())->method('defineRules');
        $this->request->expects($this->once())->method('defineMessages');
        $this->fieldTypes->expects($this->once())->method('modifyInputAfterValidation')->willReturn([]);
        $this->request->validateResolved();
    }

    public function testRulesOrder()
    {
        $this->request->setRules('field1', ['rule']);
        $this->request->setRules('field2', ['rule']);
        $this->request->setRules('field3', ['rule']);

        $this->assertEquals(['field1', 'field2', 'field3'], array_keys($this->request->rules()));
    }

    public function testOverrideRulesOrder()
    {
        $this->request->setRules('field1', ['rule']);
        $this->request->setRules('field2', ['rule']);
        $this->request->setRules('field3', ['rule']);

        $order = ['field3', 'field1', 'field2'];
        $this->request->setFieldOrder($order);

        $this->assertEquals($order, array_keys($this->request->rules()));
    }

    public function testRulesOrderWithFields()
    {
        $this->fieldTypes->method('setInputsFor')->willReturn(collect(['field2']));
        $this->fieldTypeRules->put('field2', collect('rule'));

        $this->request->setRules('field1', ['rule']);
        $this->request->setInputsFor('field2', ['rule']);
        $this->request->setRules('field3', ['rule']);

        $this->assertEquals(['field1', 'field2', 'field3'], array_keys($this->request->rules()));
    }

    public function testGetsRulesFromFields()
    {
        $this->fieldTypeRules->put('field', collect(['rule']));

        $this->assertEquals(['field' => ['rule']], $this->request->rules());
    }

    /***************************************************************************************************/

    /** @var FieldTypesRequest */
    private $request;
    private $fieldTypes;
    private $fieldTypeRules;
    private $fieldTypeMessages;

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $this->fieldTypes = $this->createMock(FieldTypes::class);

        $this->fieldTypeRules = collect();
        $this->fieldTypeMessages = collect();

        $this->fieldTypes->method('getRules')->willReturn($this->fieldTypeRules);
        $this->fieldTypes->method('getMessages')->willReturn($this->fieldTypeMessages);

        $this->request = $this->getMockForAbstractClass(
            AuthorizedRequestStub::class,
            [$this->fieldTypes]
        );
        $this->request->setContainer($app);
    }
}
