<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/acl/src/lmbAcl.class.php');

class lmbAclTest extends UnitTestCase
{
  /**
   * @var lmbAcl
   */
  public $acl;

  function setUp()
  {
    $this->acl = new lmbAcl();
  }

  function testAddAndGetRoles()
  {
    $this->assertIdentical(count($this->acl->getRoles()), 0);
    $this->assertFalse($this->acl->isRoleExist('guest'));
    $this->acl->addRole('guest');
    $this->assertIdentical(count($this->acl->getRoles()), 1);
    $this->assertTrue($this->acl->isRoleExist('guest'));
  }

  function testAddRole_Duplicate()
  {
    $this->acl->addRole('guest');
    try
    {
      $this->acl->addRole('guest');
      $this->fail();
    }
    catch (Exception $e)
    {
      $this->pass();
    }
  }

  function testRoleInherits()
  {
    $acl = $this->acl;

    $acl->addRole('guest');
    $this->assertIdentical($acl->getRoleInherits('guest'), array());

    $acl->addRole('member', 'guest');
    $this->assertIdentical($acl->getRoleInherits('member'), array('guest'));

    $acl->addRole('admin', 'member');
    $inherits = $acl->getRoleInherits('admin');
    $this->assertTrue(in_array('member', $inherits));
    $this->assertTrue(in_array('guest', $inherits));
  }

  function testRoleInheritsOnNotExistedRole()
  {
    try {
      $this->acl->addRole('guest','tiabaltu');
      $this->fail();
    } catch(lmbAclException $e) {
      $this->pass();
    }
  }

  function testRoleInheritsMultiple()
  {
    $acl = new lmbAcl();

    $acl->addRole('guest');
    $acl->addRole('member');
    $acl->addRole('admin', array('guest', 'member'));

    $inherits = $acl->getRoleInherits('admin');
    $this->assertTrue(in_array('member', $inherits));
    $this->assertTrue(in_array('guest', $inherits));
  }

  function testAddAndGetResources()
  {
    $this->assertIdentical(count($this->acl->getResources()), 0);
    $this->assertFalse($this->acl->isResourceExist('content'));
    $this->acl->addResource('content');
    $this->assertIdentical(count($this->acl->getResources()), 1);
    $this->assertTrue($this->acl->isResourceExist('content'));
  }

  function testAddResource_Duplicate()
  {
    $this->acl->addResource('content');
    try
    {
      $this->acl->addResource('content');
      $this->fail();
    }
    catch (Exception $e)
    {
      $this->pass();
    }
  }

  function testResourceInherits()
  {
    $acl = new lmbAcl();

    $acl->addResource('content');
    $this->assertIdentical($acl->getResourceInherits('content'), array());

    $acl->addResource('articles', 'content');
    $this->assertIdentical($acl->getResourceInherits('articles'), array('content'));

    $acl->addResource('news', 'articles');
    $inherits = $acl->getResourceInherits('news');
    $this->assertTrue(in_array('articles', $inherits));
    $this->assertTrue(in_array('content', $inherits));
  }

  function testResourceInheritsOnNotExistedResource()
  {
    try {
      $this->acl->addResource('content','tiabaltu');
      $this->fail();
    } catch(lmbAclException $e) {
      $this->pass();
    }
  }

  function testResourceInheritsMultiple()
  {
    $acl = new lmbAcl();

    $acl->addResource('content');
    $acl->addResource('articles');
    $acl->addResource('news', array('content', 'articles'));

    $inherits = $acl->getResourceInherits('news');
    $this->assertTrue(in_array('articles', $inherits));
    $this->assertTrue(in_array('content', $inherits));
  }
}
