<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/acl/src/lmbAcl.class.php');

class lmbAclAllowsTest extends UnitTestCase
{
  /**
   * @var lmbAcl
   */
  public $acl;

  function setUp()
  {
    $this->acl = new lmbAcl();
  }

  function _createUserSpyAndSecret()
  {
    $acl = new lmbAcl();
    $acl->addRole('user');
    $acl->addRole('spy', 'user');

    $acl->addResource('secret');

    return $acl;
  }

  function testIsAllowedNonExistent()
  {
    $this->acl->addRole('guest');
    $this->acl->addResource('news');

    try {
      $this->acl->isAllowed('guest', 'not exist', 'view');
      $this->fail();
    } catch (lmbAclException $e) {
      $this->pass();
    }

    try {
      $this->acl->isAllowed('not exist', 'news', 'view');
      $this->fail();
    } catch (lmbAclException $e) {
      $this->pass();
    }

    try {
      $this->acl->isAllowed('guest', 'news', 'view');
      $this->pass();
    } catch (lmbAclException $e) {
      $this->fail();
    }
  }

  function testDefaultDeny()
  {
    $this->acl->addRole('guest');
    $this->acl->addResource('news');
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'view'));
  }

  function testDefaultPolicy()
  {
    $acl = new lmbAcl(true, $default_policy = true);
    $acl->addRole('guest');
    $acl->addResource('news');
    $this->assertTrue($acl->isAllowed('guest', 'news', 'view'));
  }

  function testAllowAndDenyOnPrivelege()
  {
    $this->acl->addRole('guest');
    $this->acl->addResource('news');
    $this->acl->allow('guest', 'news', 'view');
    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'view'));
    $this->acl->deny('guest', 'news', 'view');
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'view'));
  }

  function testAllowAndDenyOnResource()
  {
    $this->acl->addRole('guest');
    $this->acl->addResource('news');
    $this->acl->allow('guest', 'news');
    $this->assertTrue($this->acl->isAllowed('guest', 'news'));
    $this->acl->deny('guest', 'news');
    $this->assertFalse($this->acl->isAllowed('guest', 'news'));
  }

  function testAllowAndDenyOnRole()
  {
    $this->acl->addRole('admin');
    $this->acl->allow('admin');
    $this->assertTrue($this->acl->isAllowed('admin'));
    $this->acl->deny('admin');
    $this->assertFalse($this->acl->isAllowed('admin'));
  }

  function testAllowAndDenyOnRoleForAllResource()
  {
    $this->acl->addRole('guest');
    $this->acl->addResource('news');
    $this->acl->allow('guest', null, 'view');
    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'view'));
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'add'));
    $this->acl->deny('guest', null, 'view');
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'view'));
  }

  function testAllowAndDenyLevelsCombinations()
  {
    $this->acl->addRole('guest');
    $this->acl->addResource('news');
    $this->acl->addResource('articles');

    $this->acl->allow('guest');
    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'view'));
    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'add'));

    $this->acl->deny('guest', 'news');
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'view'));
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'add'));

    $this->acl->allow('guest', 'news', 'view');
    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'view'));
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'add'));

    $this->acl->deny('guest', 'news');
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'view'));
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'add'));

    $this->acl->allow('guest');
    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'view'));
    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'add'));

  }

  function testAllowAndDenyWithInherits_RoleLevelRules()
  {
    $acl = $this->_createUserSpyAndSecret();

    $acl->allow('user');
    $acl->deny('spy');

    $this->assertTrue($acl->isAllowed('user'));
    $this->assertFalse($acl->isAllowed('spy'));
  }

  function testAllowAndDenyWithInherits_ResourceLevelRules()
  {
    $acl = $this->_createUserSpyAndSecret();

    $acl->allow('user', 'secret');
    $acl->deny('spy', 'secret');

    $this->assertTrue($acl->isAllowed('user', 'secret'));
    $this->assertFalse($acl->isAllowed('spy', 'secret'));
  }

  function testAllowAndDenyWithInherits_PrivelegesLevelRules()
  {
    $acl = $this->_createUserSpyAndSecret();

    $acl->allow('user', 'secret', 'view');
    $acl->deny('spy', 'secret', 'view');

    $this->assertTrue($acl->isAllowed('user', 'secret', 'view'));
    $this->assertFalse($acl->isAllowed('spy', 'secret', 'view'));
  }

  function testAllowAndDenyWithInherits_PrivelegesWithoutResource()
  {
    $acl = $this->_createUserSpyAndSecret();

    $acl->allow('user', null, 'view');
    $acl->deny('spy', null, 'view');

    $this->assertTrue($acl->isAllowed('user', 'secret', 'view'));
    $this->assertFalse($acl->isAllowed('spy', 'secret', 'view'));
  }

  function testAllowAndDenyWithInherits_DefaultInheritsPolicyisAllow()
  {
    $acl = new lmbAcl($default_inherits_policy = true);

    $acl->addRole('user');
    $acl->addRole('intruder');

    $acl->addRole('firstly user', array('user', 'intruder'));
    $acl->addRole('firstly intruder', array('intruder', 'user'));

    $acl->allow('user');
    $acl->deny('intruder');

    $this->assertTrue($acl->isAllowed('firstly user'));
    $this->assertTrue($acl->isAllowed('firstly intruder'));
  }

  function testAllowAndDenyWithInherits_DefaultInheritsPolicyisDeny()
  {
    $acl = new lmbAcl($default_inherits_policy = false);

    $acl->addRole('user');
    $acl->addRole('intruder');

    $acl->addRole('firstly user', array('user', 'intruder'));
    $acl->addRole('firstly intruder', array('intruder', 'user'));

    $acl->allow('user');
    $acl->deny('intruder');

    $this->assertFalse($acl->isAllowed('firstly user'));
    $this->assertFalse($acl->isAllowed('firstly intruder'));
  }

  function testHasDenials()
  {
    $acl = new lmbAcl();

    $acl->addRole('intruder');
    $acl->addRole('spy', 'intruder');

    $acl->deny('intruder');

    $this->assertTrue($acl->hasDenials('intruder'));
    $this->assertTrue($acl->hasDenials('spy'));
  }

  function testHasAllows()
  {
    $acl = new lmbAcl();

    $acl->addRole('guest');
    $acl->addRole('user', 'guest');

    $acl->allow('guest');

    $this->assertTrue($acl->hasAllows('guest'));
    $this->assertTrue($acl->hasAllows('user'));
  }

  function testResourceInherits_WithPrivelegies()
  {
    $acl = new lmbAcl();
    $acl->addRole('user');

    $acl->addResource('news');
    $acl->addResource('secret', 'news');

    $acl->allow('user', 'news', 'view');

    $this->assertTrue($acl->isAllowed('user', 'secret', 'view'));
  }

  function testResourceInheritsAndRoleInheritsOverlap()
  {
    $acl = new lmbAcl();
    $acl->addRole('user');
    $acl->addRole('fbi', 'user');

    $acl->addResource('news');
    $acl->addResource('secret', 'news');

    $acl->allow('user', 'news', 'view');
    $acl->deny('user', 'secret', 'view');

    $this->assertTrue($acl->isAllowed('user', 'news', 'view'));
    $this->assertFalse($acl->isAllowed('user', 'secret', 'view'));
    $this->assertTrue($acl->isAllowed('fbi', 'news', 'view'));
    // role inherits and resource inherits conflict, role inherits should have the priority
    $this->assertFalse($acl->isAllowed('fbi', 'secret', 'view'));
  }

  function testHasAllowsAndHasDenialsWithResourceAndRoleInherits()
  {
    $acl = $this->acl;

    $acl->addRole('caveman');
    $acl->addRole('russian', 'caveman');

    $acl->addResource('food');
    $acl->addResource('meat', 'food');

    $acl->addResource('water');
    $acl->addResource('vodka', 'water');

    $acl->allow('caveman', 'food');
    $acl->allow('russian', 'water');

    $this->assertFalse($acl->hasDenials('caveman', 'food'));
    $this->assertFalse($acl->hasDenials('caveman', 'meat'));
    $this->assertFalse($acl->hasDenials('caveman', 'water'));
    $this->assertFalse($acl->hasDenials('caveman', 'vodka')); // fixed here

    $this->assertFalse($acl->hasDenials('russian', 'food'));
    $this->assertFalse($acl->hasDenials('russian', 'meat'));
    $this->assertFalse($acl->hasDenials('russian', 'water'));
    $this->assertFalse($acl->hasDenials('russian', 'vodka')); // here

    $this->assertTrue($acl->hasAllows('caveman', 'food'));
    $this->assertTrue($acl->hasAllows('caveman', 'meat'));
    $this->assertFalse($acl->hasAllows('caveman', 'water'));
    $this->assertFalse($acl->hasAllows('caveman', 'vodka'));

    $this->assertTrue($acl->hasAllows('russian', 'food'));
    $this->assertTrue($acl->hasAllows('russian', 'meat'));
    $this->assertTrue($acl->hasAllows('russian', 'water'));
    $this->assertTrue($acl->hasAllows('russian', 'vodka')); // and here
  }
}
