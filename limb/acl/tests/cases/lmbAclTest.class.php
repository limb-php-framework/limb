<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
    $acl = $this->acl;

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

  function testResourceInherits()
  {
    $acl = $this->acl;

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
    $acl = $this->acl;

    $acl->addResource('content');
    $acl->addResource('articles');
    $acl->addResource('news', array('content', 'articles'));

    $inherits = $acl->getResourceInherits('news');
    $this->assertTrue(in_array('articles', $inherits));
    $this->assertTrue(in_array('content', $inherits));
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

  function testAllowAndDenyWithInherits()
  {
    $this->acl->addRole('guest');
    $this->acl->addRole('member', 'guest');

    $this->acl->addResource('news');

    $this->acl->allow('guest', 'news', 'view');
    $this->acl->allow('member', 'news', 'vote');

    $this->assertTrue($this->acl->isAllowed('guest', 'news', 'view'));
    $this->assertFalse($this->acl->isAllowed('guest', 'news', 'vote'));

    $this->assertTrue($this->acl->isAllowed('member', 'news', 'view'));
    $this->assertTrue($this->acl->isAllowed('member', 'news', 'vote'));
  }

  function testBlackBoxTest()
  {
    $acl = $this->acl;

    $acl->addResource('content');

    $acl->addRole('guest');
    $acl->addRole('staff', 'guest');
    $acl->addRole('editor', 'staff');
    $acl->addRole('administrator');

    // Guest may only view content
    $acl->allow('guest', null, 'view');

    // Staff inherits view privilege from guest, but also needs additional privileges
    $acl->allow('staff', null, array('edit', 'submit', 'revise'));

    // Editor inherits view, edit, submit, and revise privileges, but also needs additional privileges
    $acl->allow('editor', null, array('publish', 'archive', 'delete'));

    // Administrator inherits nothing but is allowed all privileges
    $acl->allow('administrator');

    // Access control checks based on above permission sets

    $this->assertTrue($acl->isAllowed('guest', 'content', 'view'));
    $this->assertFalse($acl->isAllowed('guest', 'content', 'edit'));
    $this->assertFalse($acl->isAllowed('guest', 'content', 'submit'));
    $this->assertFalse($acl->isAllowed('guest', 'content', 'revise'));
    $this->assertFalse($acl->isAllowed('guest', 'content', 'publish'));
    $this->assertFalse($acl->isAllowed('guest', 'content', 'archive'));
    $this->assertFalse($acl->isAllowed('guest', 'content', 'delete'));
    $this->assertFalse($acl->isAllowed('guest', 'content', 'unknown'));
    $this->assertFalse($acl->isAllowed('guest'));

    $this->assertTrue($acl->isAllowed('staff', 'content', 'view'));
    $this->assertTrue($acl->isAllowed('staff', 'content', 'edit'));
    $this->assertTrue($acl->isAllowed('staff', 'content', 'submit'));
    $this->assertTrue($acl->isAllowed('staff', 'content', 'revise'));
    $this->assertFalse($acl->isAllowed('staff', 'content', 'publish'));
    $this->assertFalse($acl->isAllowed('staff', 'content', 'archive'));
    $this->assertFalse($acl->isAllowed('staff', 'content', 'delete'));
    $this->assertFalse($acl->isAllowed('staff', 'content', 'unknown'));
    $this->assertFalse($acl->isAllowed('staff'));

    $this->assertTrue($acl->isAllowed('editor', 'content', 'view'));
    $this->assertTrue($acl->isAllowed('editor', 'content', 'edit'));
    $this->assertTrue($acl->isAllowed('editor', 'content', 'submit'));
    $this->assertTrue($acl->isAllowed('editor', 'content', 'revise'));
    $this->assertTrue($acl->isAllowed('editor', 'content', 'publish'));
    $this->assertTrue($acl->isAllowed('editor', 'content', 'archive'));
    $this->assertTrue($acl->isAllowed('editor', 'content', 'delete'));
    $this->assertFalse($acl->isAllowed('editor', 'content', 'unknown'));
    $this->assertFalse($acl->isAllowed('editor'));

    $this->assertTrue($acl->isAllowed('administrator', 'content', 'view'));
    $this->assertTrue($acl->isAllowed('administrator', 'content', 'edit'));
    $this->assertTrue($acl->isAllowed('administrator', 'content', 'submit'));
    $this->assertTrue($acl->isAllowed('administrator', 'content', 'revise'));
    $this->assertTrue($acl->isAllowed('administrator', 'content', 'publish'));
    $this->assertTrue($acl->isAllowed('administrator', 'content', 'archive'));
    $this->assertTrue($acl->isAllowed('administrator', 'content', 'delete'));
    $this->assertTrue($acl->isAllowed('administrator', 'content', 'unknown'));
    $this->assertTrue($acl->isAllowed('administrator'));

    // Some checks on specific areas, which inherit access controls from the root ACL node
    $acl->addResource('newsletter');
    $acl->addResource('pending', 'newsletter');
    $acl->addResource('gallery');
    $acl->addResource('profiles', 'gallery');
    $acl->addResource('config');
    $acl->addResource('hosts', 'config');

    $this->assertTrue($acl->isAllowed('guest', 'pending', 'view'));
    $this->assertTrue($acl->isAllowed('staff', 'profiles', 'revise'));
    $this->assertTrue($acl->isAllowed('staff', 'pending', 'view'));
    $this->assertTrue($acl->isAllowed('staff', 'pending', 'edit'));
    $this->assertFalse($acl->isAllowed('staff', 'pending', 'publish'));
    $this->assertFalse($acl->isAllowed('staff', 'pending'));
    $this->assertFalse($acl->isAllowed('editor', 'hosts', 'unknown'));
    $this->assertTrue($acl->isAllowed('administrator', 'pending'));

    // Add a new group, marketing, which bases its permissions on staff
    $acl->addRole('marketing', 'staff');

    // Refine the privilege sets for more specific needs

    // Allow marketing to publish and archive newsletters
    $acl->allow('marketing', 'newsletter', array('publish', 'archive'));

    // Allow marketing to publish and archive latest news
    $acl->addResource('news');
    $acl->addResource('latest', 'news');
    $acl->allow('marketing', 'latest', array('publish', 'archive'));

    // Deny staff (and marketing, by inheritance) rights to revise latest news
    $acl->deny('staff', 'latest', 'revise');

    $acl->addResource('announcement', 'news');

    $this->assertTrue($acl->isAllowed('marketing', 'content', 'view'));
    $this->assertTrue($acl->isAllowed('marketing', 'content', 'edit'));
    $this->assertTrue($acl->isAllowed('marketing', 'content', 'submit'));
    $this->assertTrue($acl->isAllowed('marketing', 'content', 'revise'));
    $this->assertFalse($acl->isAllowed('marketing', 'content', 'publish'));
    $this->assertFalse($acl->isAllowed('marketing', 'content', 'archive'));
    $this->assertFalse($acl->isAllowed('marketing', 'content', 'delete'));
    $this->assertFalse($acl->isAllowed('marketing', 'content', 'unknown'));
    $this->assertFalse($acl->isAllowed('marketing'));

    $this->assertTrue($acl->isAllowed('marketing', 'newsletter', 'publish'));
    $this->assertFalse($acl->isAllowed('staff', 'pending', 'publish'));
    $this->assertTrue($acl->isAllowed('marketing', 'newsletter', 'archive'));
    $this->assertFalse($acl->isAllowed('marketing', 'newsletter', 'delete'));
    $this->assertFalse($acl->isAllowed('marketing', 'newsletter'));

    $this->assertTrue($acl->isAllowed('marketing', 'latest', 'publish'));
    $this->assertTrue($acl->isAllowed('marketing', 'latest', 'archive'));
    $this->assertFalse($acl->isAllowed('marketing', 'latest', 'delete'));
    $this->assertFalse($acl->isAllowed('marketing', 'latest', 'revise'));
    $this->assertFalse($acl->isAllowed('marketing', 'latest'));

    $this->assertFalse($acl->isAllowed('marketing', 'announcement', 'archive'));
    $this->assertFalse($acl->isAllowed('staff', 'announcement', 'archive'));

    $this->assertFalse($acl->isAllowed('staff', 'latest', 'publish'));

    $acl->allow('marketing', 'latest');

    $this->assertTrue($acl->isAllowed('marketing', 'latest', 'archive'));
    $this->assertTrue($acl->isAllowed('marketing', 'latest', 'publish'));
    $this->assertTrue($acl->isAllowed('marketing', 'latest', 'edit'));
    $this->assertTrue($acl->isAllowed('marketing', 'latest'));
  }

}
