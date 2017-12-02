<?php

namespace RickSelby\Tests;

use Illuminate\Support\Collection;
use RickSelby\Tests\Stubs\AuthorizedRequestStub;
use RickSelby\LaravelRequestFieldTypes\FieldTypes;
use RickSelby\LaravelRequestFieldTypes\FieldTypesRequest;

class FieldTypesRequestTest extends AbstractTestCase
{
    /** @var FieldTypesRequest */
    private $request;
    private $fieldTypes;
    private $fieldTypeRules;

    public function testRulesFormat()
    {
        $this->request->setRules('field', ['rule1', 'rule2']);
        $this->assertEquals(['field' => 'rule1|rule2'], $this->request->rules());
    }

    public function testValidate()
    {
        $this->request->expects($this->once())->method('defineRules');
        $this->fieldTypes->expects($this->once())->method('modifyInputAfterValidation')->willReturn([]);
        $this->request->validate();
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

    public function testGetsRulesFromFields()
    {
        $this->fieldTypeRules->put('field', collect(['rule']));

        $this->assertEquals(['field' => 'rule'], $this->request->rules());
    }

    /***************************************************************************************************/

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->fieldTypes = $this->createMock(FieldTypes::class);

        $this->fieldTypeRules = collect();

        $this->fieldTypes->method('getRules')->willReturn($this->fieldTypeRules);

        $this->request = $this->getMockForAbstractClass(
            AuthorizedRequestStub::class,
            [$this->fieldTypes]
        );
        $this->request->setContainer($app);
    }
}
