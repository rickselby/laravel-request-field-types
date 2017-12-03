<?php

namespace RickSelby\Tests;

use Illuminate\Support\Collection;
use RickSelby\LaravelRequestFieldTypes\RulesTrait;

class RulesTraitTest extends AbstractTestCase
{
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
        $this->assertEquals(collect(['rule']), $rules->first());
    }

    public function testSetMultipleRules()
    {
        $this->rulesTrait->setRules('field', ['rule1']);
        $this->rulesTrait->setRules('field', ['rule2']);

        $rules = $this->rulesTrait->getRules();

        $this->assertEquals(1, $rules->count());
        $this->assertEquals('field', $rules->keys()->first());
        $this->assertEquals(collect(['rule1', 'rule2']), $rules->first());
    }

    public function testSetMultipleRulesNoDuplicated()
    {
        $this->rulesTrait->setRules('field', ['rule1', 'rule2']);
        $this->rulesTrait->setRules('field', ['rule2', 'rule3']);

        $rules = $this->rulesTrait->getRules();

        $this->assertEquals(1, $rules->count());
        $this->assertEquals('field', $rules->keys()->first());
        $this->assertEquals(collect(['rule1', 'rule2', 'rule3']), $rules->first());
    }

    /***************************************************************************************************/

    /** @var RulesTrait */
    private $rulesTrait;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $this->rulesTrait = $this->getMockForTrait(RulesTrait::class);
    }
}
