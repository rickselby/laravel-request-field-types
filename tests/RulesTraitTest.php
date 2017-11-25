<?php

namespace RickSelby\Tests;

use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\FieldTypes;
use RickSelby\LaravelRequestFieldTypes\RulesTrait;
use RickSelby\Tests\Stubs\MappedStub;
use RickSelby\Tests\Stubs\RulesTraitStub;
use RickSelby\Tests\Stubs\UnmappedStub;
use RickSelby\Tests\Stubs\MappedStubPresenter;
use RickSelby\LaravelAutoPresenterMapper\AutoPresenterMapper;

class RulesTraitTest extends AbstractTestCase
{
    /** @var RulesTrait */
    private $rulesTrait;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $this->rulesTrait = $this->getMockForTrait(RulesTrait::class);
    }

    public function testGetRulesIsCollection()
    {
        $this->assertInstanceOf(Collection::class, $this->rulesTrait->getRules());
    }

    public function testSetRules()
    {
        $this->rulesTrait->setRules('field', ['rule']);

        $rules = $this->rulesTrait->getRules();

        $this->assertEquals(1, $rules->count());
        $this->assertEquals('field', $rules->keys()->first());
        $this->assertEquals(['rule'], $rules->first());
    }
}
