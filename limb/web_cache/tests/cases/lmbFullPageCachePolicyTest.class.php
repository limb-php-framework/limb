<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_cache/src/lmbFullPageCacheRule.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCachePolicy.class.php');
lmb_require('limb/core/src/lmbObject.class.php');

Mock :: generate('lmbFullPageCacheRule', 'MockFullPageCacheRule');

class lmbFullPageCachePolicyTest extends UnitTestCase
{
  protected $policy;

  function setUp()
  {
    $this->policy = new lmbFullPageCachePolicy();
  }

  function testFindRule()
  {
    $rule = new lmbFullPageCacheRule();

    $r1 = new MockFullPageCacheRule();
    $r2 = new MockFullPageCacheRule();
    $r3 = new MockFullPageCacheRule();

    $request = new lmbObject();

    $r1->expectOnce('isSatisfiedBy', array($request));
    $r1->setReturnValue('isSatisfiedBy', false, array($request));

    $r2->expectOnce('isSatisfiedBy', array($request));
    $r2->setReturnValue('isSatisfiedBy', true, array($request));

    $r3->expectNever('isSatisfiedBy');

    $this->policy->addRuleset($r1);
    $this->policy->addRuleset($r2);
    $this->policy->addRuleset($r3);

    $this->assertReference($r2, $this->policy->findRuleset($request));
  }
}


