<?php

namespace RickSelby\Tests;

use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\BaseFieldType;

class BaseFieldTypeTest extends AbstractTestCase
{
    /** @var BaseFieldType */
    private $baseFieldType;

    public function testGetIdentifier()
    {
        $this->setProtectedProperty($this->baseFieldType, 'identifier', 'ID');
        $this->assertEquals('ID', $this->baseFieldType->getIdentifier());
    }

    public function testSetInputFieldsSimple()
    {
        $this->baseFieldType->setInputFields(['field']);
        $this->assertEquals(new Collection(['field' => ['rule']]), $this->baseFieldType->getRules());
    }

    public function testSetInputFieldsWithExtraRules()
    {
        $this->baseFieldType->setInputFields(['field' => 'rule2']);
        $this->assertEquals(new Collection(['field' => ['rule2', 'rule']]), $this->baseFieldType->getRules());
    }

    public function testSetInputFieldsWithBoth()
    {
        $this->baseFieldType->setInputFields(['simplefield', 'field' => 'rule2']);
        $this->assertEquals(
            new Collection([
                'simplefield' => ['rule'],
                'field' => ['rule2', 'rule'],
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

    /***************************************************************************************************/

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->baseFieldType = $this->getMockForAbstractClass(BaseFieldType::class);
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
