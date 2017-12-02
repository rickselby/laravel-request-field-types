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

    public function testRulesFormat()
    {
        $this->assertEquals(['field' => 'rule1|rule2'], $this->request->rules());
    }

    public function testValidate()
    {
        $this->request->expects($this->once())->method('defineRules');
        $this->fieldTypes->expects($this->once())->method('modifyInputAfterValidation')->willReturn([]);
        $this->request->validate();
    }

    /***************************************************************************************************/

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->fieldTypes = $this->createMock(FieldTypes::class);
        $this->fieldTypes->method('getRules')->willReturn(new Collection(['field' => ['rule1', 'rule2']]));

        $this->request = $this->getMockForAbstractClass(
            AuthorizedRequestStub::class,
            [$this->fieldTypes]
        );
        $this->request->setContainer($app);
    }
}
