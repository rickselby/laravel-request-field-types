<?php

namespace RickSelby\Tests;

use RickSelby\Tests\Stubs\TouchedFieldTypeStub;
use RickSelby\LaravelRequestFieldTypes\BaseFieldType;

class BaseFieldTypeInputTest extends AbstractTestCase
{
    /** @var BaseFieldType */
    private $baseFieldType;

    public function testModifyInputDoesntAlterWithNoKeys()
    {
        $output = $this->baseFieldType->modifyInputAfterValidation(['field' => 'value']);
        $this->assertEquals(['field' => 'value'], $output);
    }

    public function testModifyInputRunsFunction()
    {
        $this->baseFieldType->setInputFields(['field']);
        $output = $this->baseFieldType->modifyInputAfterValidation(['field' => 'value']);
        $this->assertEquals(['field' => 'touched'], $output);
    }

    public function testModifyInputOnlyModifiesInputField()
    {
        $this->baseFieldType->setInputFields(['field']);
        $output = $this->baseFieldType->modifyInputAfterValidation(['field' => 'value', 'field2' => 'value']);
        $this->assertEquals(['field' => 'touched', 'field2' => 'value'], $output);
    }

    public function testModifyInputOnlyModifiesMultipleFields1()
    {
        $this->baseFieldType->setInputFields(['field.*']);
        $output = $this->baseFieldType->modifyInputAfterValidation([
            'field' => [
                'a' => 'value',
                'b' => 'value',
            ],
            'field2' => [
                'a' => 'value',
                'b' => 'value',
            ],
        ]);
        $this->assertEquals([
            'field' => [
                'a' => 'touched',
                'b' => 'touched',
            ],
            'field2' => [
                'a' => 'value',
                'b' => 'value',
            ],
        ], $output);
    }

    public function testModifyInputOnlyModifiesMultipleFields2()
    {
        $this->baseFieldType->setInputFields(['field.*.change']);
        $output = $this->baseFieldType->modifyInputAfterValidation([
            'field' => [
                'a' => [
                    'change' => 'value',
                    'leave' => 'value',
                ],
                'b' => [
                    'change' => 'value',
                    'leave' => 'value',
                ],
            ],
        ]);
        $this->assertEquals([
            'field' => [
                'a' => [
                    'change' => 'touched',
                    'leave' => 'value',
                ],
                'b' => [
                    'change' => 'touched',
                    'leave' => 'value',
                ],
            ],
        ], $output);
    }

    /***************************************************************************************************/

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->baseFieldType = $this->getMockForAbstractClass(TouchedFieldTypeStub::class);
        $this->baseFieldType->expects($this->any())->method('rules')->willReturn(['rule']);
    }

    public function setProtectedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
}
