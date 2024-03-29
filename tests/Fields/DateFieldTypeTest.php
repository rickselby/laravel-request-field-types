<?php

namespace RickSelby\Tests\Fields;

use Carbon\Carbon;
use RickSelby\LaravelRequestFieldTypes\Fields\DateFieldType;
use RickSelby\Tests\AbstractTestCase;

class DateFieldTypeTest extends AbstractTestCase
{
    /** @var DateFieldType */
    private $fieldType;

    public function testRules()
    {
        $this->fieldType->setInputFields(['field']);
        $this->assertEquals(
            collect(['field' => collect(['date_format:"Y-m-d"'])]),
            $this->fieldType->getRules()
        );
    }

    public function testFormattingDate()
    {
        $this->fieldType->setInputFields(['field']);

        $output = $this->fieldType->modifyInputAfterValidation(['field' => '2017-09-09']);

        $this->assertEquals(
            Carbon::create(2017, 9, 9)->startOfDay(),
            $output['field']
        );
    }

    /***************************************************************************************************/

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $this->fieldType = new DateFieldType();
    }
}
