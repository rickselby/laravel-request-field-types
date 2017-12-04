<?php

namespace RickSelby\Tests;

use RickSelby\Tests\Stubs\IDFieldTypeStub;
use RickSelby\LaravelRequestFieldTypes\BaseFieldType;

class BaseFieldTypeTest extends AbstractTestCase
{
    /** @var BaseFieldType */
    private $baseFieldType;

    public function testGetIdentifier()
    {
        $this->assertEquals('ID', $this->baseFieldType->getIdentifier());
    }

    public function testSetInputFieldsSimple()
    {
        $this->baseFieldType->setInputFields(['field']);
        $this->assertEquals(collect(['field' => collect(['rule'])]), $this->baseFieldType->getRules());
    }

    public function testSetInputFieldsWithExtraRules()
    {
        $this->baseFieldType->setInputFields(['field' => 'rule2']);
        $this->assertEquals(collect(['field' => collect(['rule2', 'rule'])]), $this->baseFieldType->getRules());
    }

    public function testSetInputFieldsSimpleReturnsFieldNames()
    {
        $this->assertEquals(
            collect(['field1', 'field2']),
            $this->baseFieldType->setInputFields(['field1', 'field2' => 'rule'])
        );
    }

    public function testSetInputFieldsWithBoth()
    {
        $this->baseFieldType->setInputFields(['simplefield', 'field' => 'rule2']);
        $this->assertEquals(
            collect([
                'simplefield' => collect(['rule']),
                'field' => collect(['rule2', 'rule']),
            ]),
            $this->baseFieldType->getRules()
        );
    }

    public function testModifyInputDoesntAlterWithNoKeys()
    {
        $output = $this->baseFieldType->modifyInputAfterValidation(['field' => 'value']);
        $this->assertEquals(['field' => 'value'], $output);
    }

    public function testDefaultClassDoesntAlterInput()
    {
        $this->baseFieldType->setInputFields(['field']);
        $output = $this->baseFieldType->modifyInputAfterValidation(['field' => 'value']);
        $this->assertEquals(['field' => 'value'], $output);
    }

    public function testSetMessagesForIsCalled()
    {
        $this->baseFieldType->expects($this->once())->method('setMessagesFor');
        $this->baseFieldType->setInputFields(['field']);
    }

    /***************************************************************************************************/

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->baseFieldType = $this->getMockForAbstractClass(IDFieldTypeStub::class);
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
