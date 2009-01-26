<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_cache/src/lmbFullPageCacheRuleset.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRule.class.php');
lmb_require('limb/core/src/lmbObject.class.php');

Mock :: generate('lmbFullPageCacheRule', 'MockFullPageCacheRule');

class lmbFullPageCacheRulesetTest extends UnitTestCase
{
  function testSetType()
  {
    $set = new lmbFullPageCacheRuleset();
    $this->assertTrue($set->isAllow());
    $this->assertFalse($set->isDeny());

    $set->setType(false);
    $this->assertFalse($set->isAllow());
    $this->assertTrue($set->isDeny());
  }

  function testIsSatisfied()
  {
    $r1 = new MockFullPageCacheRule();
    $r2 = new MockFullPageCacheRule();

    $r1->expectOnce('isSatisfiedBy', array($request = new lmbObject()));
    $r1->setReturnValue('isSatisfiedBy', true);

    $r2->expectOnce('isSatisfiedBy', array($request = new lmbObject()));
    $r2->setReturnValue('isSatisfiedBy', true);

    $set = new lmbFullPageCacheRuleset();
    $set->addRule($r1);
    $set->addRule($r2);

    $this->assertTrue($set->isSatisfiedBy($request));
  }

  function testIsNotSatisfied()
  {
    $r1 = new MockFullPageCacheRule();
    $r2 = new MockFullPageCacheRule();

    $r1->expectOnce('isSatisfiedBy', array($request = new lmbObject()));
    $r1->setReturnValue('isSatisfiedBy', false);

    $r2->expectNever('isSatisfiedBy');

    $set = new lmbFullPageCacheRuleset();
    $set->addRule($r1);
    $set->addRule($r2);

    $this->assertFalse($set->isSatisfiedBy($request));
  }
}


