<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/acl/src/lmbAclConfigurator.class.php');

class lmbAclConfiguratorTest extends UnitTestCase
{
  /**
   * @var array
   */
  public $options;

  function setUp()
  {
    $this->options = array('');
  }

  function testAddRoles()
  {
    $configurator = new lmbAclConfigurator(array('roles' => array('a' => null, 'b' => null)));
    $acl = $configurator->getAcl();
    $this->assertIdentical(count($acl->getRoles()), 2);
    $this->assertTrue($acl->isRoleExist('a'));
    $this->assertTrue($acl->isRoleExist('b'));
  }
  
  function testAddInheritedRoles()
  {
    $configurator = new lmbAclConfigurator(array('roles' => array('a' => null, 'b1' => 'a', 'b2' => array('a'), 'c' => array('b1', 'b2'))));
    $acl = $configurator->getAcl();
    $this->assertIdentical(count($acl->getRoles()), 4);
    $this->assertIdentical($acl->getRoleInherits('b1'), array('a'));
    $this->assertIdentical($acl->getRoleInherits('b2'), array('a'));
	
    $inherits = $acl->getRoleInherits('c');
    $this->assertTrue(in_array('b1', $inherits));
    $this->assertTrue(in_array('b2', $inherits));
  }
  
  function testAddResources()
  {
    $configurator = new lmbAclConfigurator(array('resources' => array('a' => null, 'b' => null)));
    $acl = $configurator->getAcl();
	
    $this->assertIdentical(count($acl->getResources()), 2);
    $this->assertTrue($acl->isResourceExist('a'));
    $this->assertTrue($acl->isResourceExist('b'));
  }
  
  function testAddInheritedResources()
  {
    $configurator = new lmbAclConfigurator(array('resources' => array('a' => null, 'b' => array('a'), 'c' => array('b'))));
    $acl = $configurator->getAcl();

    $this->assertIdentical($acl->getResourceInherits('a'), array());
    $this->assertIdentical($acl->getResourceInherits('b'), array('a'));
    $inherits = $acl->getResourceInherits('c');
    $this->assertTrue(in_array('a', $inherits));
    $this->assertTrue(in_array('b', $inherits));
  }
  
  function testAllowRule()
  {
    $options = array(
      'roles' => array(
        'guest' => null,
      ),
      'resources' => array(
        'article' => null,
        'news' => null
      ),
      'allow' => array(
        'guest' => array(
          'article' => 'read',
          'news' => array('read', 'comment')
        )
      )
    );
    
    $configurator = new lmbAclConfigurator($options);
    $acl = $configurator->getAcl();
    $this->assertTrue($acl->isAllowed('guest', 'article', 'read'));
    $this->assertTrue($acl->isAllowed('guest', 'news', 'read'));
    $this->assertTrue($acl->isAllowed('guest', 'news', 'comment'));
  }
  
  function testAllowRuleWithMask()
  {
    $options = array(
      'roles' => array(
        'guest' => null,
      ),
      'resources' => array(
        'article' => null
      )
    );
    
    $configurator = new lmbAclConfigurator(array_merge($options, array('allow' => array('guest' => array()))));
    $this->assertTrue($configurator->getAcl()->isAllowed('guest'));
    $configurator = new lmbAclConfigurator(array_merge($options, array('allow' => array('guest' => 'article'))));
    $this->assertTrue($configurator->getAcl()->isAllowed('guest', 'article'));
    $configurator = new lmbAclConfigurator(array_merge($options, array('allow' => array('guest' => array('*' => 'read')))));
    $this->assertTrue($configurator->getAcl()->isAllowed('guest', null, 'read'));
  }
  
  function testTurnOffDefaultInheritsPolicy()
  {
    $options = array(
      'roles' => array('a' => null, 'b' => array('a')),
      'allow' => array('a' => array())
	  );
    $configurator = new lmbAclConfigurator($options);
    $this->assertTrue($configurator->getAcl()->isAllowed('b'));
	  
    $configurator = new lmbAclConfigurator(array_merge(array('default_inherits_policy' => false), $options));
    $this->assertFalse($configurator->getAcl()->isAllowed('b'));
  }
  
  function testTurnOnDefaultAllowPolicy()
  {
    $options = array(
      'roles' => array('guest' => null),
      'resources' => array('article' => null)
	  );
    
    $configurator = new lmbAclConfigurator($options);
    $this->assertFalse($configurator->getAcl()->isAllowed('guest', 'article'));
	
    $configurator = new lmbAclConfigurator(array_merge(array('default_allow_policy' => true), $options));
    $this->assertTrue($configurator->getAcl()->isAllowed('guest', 'article', 'read'));
  }
}
