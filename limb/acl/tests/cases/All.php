<?php
class Zend_AclTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that basic removal of a single Role works
     *
     * @return void
     */
    public function testRoleRegistryRemoveOne()
    {
        $roleGuest = new Zend_Acl_Role('guest');
        $this->_acl->addRole($roleGuest)
                   ->removeRole($roleGuest);
        $this->assertFalse($this->_acl->hasRole($roleGuest));
    }

    /**
     * Ensures that an exception is thrown when a non-existent Role is specified for removal
     *
     * @return void
     */
    public function testRoleRegistryRemoveOneNonExistent()
    {
        try {
            $this->_acl->removeRole('nonexistent');
            $this->fail('Expected Zend_Acl_Role_Registry_Exception not thrown upon removing a non-existent Role');
        } catch (Zend_Acl_Role_Registry_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    /**
     * Ensures that removal of all Roles works
     *
     * @return void
     */
    public function testRoleRegistryRemoveAll()
    {
        $roleGuest = new Zend_Acl_Role('guest');
        $this->_acl->addRole($roleGuest)
                   ->removeRoleAll();
        $this->assertFalse($this->_acl->hasRole($roleGuest));
    }

    /**
     * Ensures that basic removal of a single Resource works
     *
     * @return void
     */
    public function testResourceRemoveOne()
    {
        $resourceArea = new Zend_Acl_Resource('area');
        $this->_acl->add($resourceArea)
                   ->remove($resourceArea);
        $this->assertFalse($this->_acl->has($resourceArea));
    }

    /**
     * Ensures that an exception is thrown when a non-existent Resource is specified for removal
     *
     * @return void
     */
    public function testResourceRemoveOneNonExistent()
    {
        try {
            $this->_acl->remove('nonexistent');
            $this->fail('Expected Zend_Acl_Exception not thrown upon removing a non-existent Resource');
        } catch (Zend_Acl_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    /**
     * Ensures that removal of all Resources works
     *
     * @return void
     */
    public function testResourceRemoveAll()
    {
        $resourceArea = new Zend_Acl_Resource('area');
        $this->_acl->add($resourceArea)
                   ->removeAll();
        $this->assertFalse($this->_acl->has($resourceArea));
    }

    public function testRolePrivilegeAssert()
    {
        $roleGuest = new Zend_Acl_Role('guest');
        $this->_acl->addRole($roleGuest)
                   ->allow($roleGuest, null, 'somePrivilege', new Zend_AclTest_AssertTrue());
        $this->assertTrue($this->_acl->isAllowed($roleGuest, null, 'somePrivilege'));
        $this->_acl->allow($roleGuest, null, 'somePrivilege', new Zend_AclTest_AssertFalse());
        $this->assertFalse($this->_acl->isAllowed($roleGuest, null, 'somePrivilege'));
    }

    /**
     * Ensures that removing the default deny rule results in default deny rule
     *
     * @return void
     */
    public function testRemoveDefaultDeny()
    {
        $this->assertFalse($this->_acl->isAllowed());
        $this->_acl->removeDeny();
        $this->assertFalse($this->_acl->isAllowed());
    }

    /**
     * Ensures that removing the default deny rule results in assertion method being removed
     *
     * @return void
     */
    public function testRemoveDefaultDenyAssert()
    {
        $this->_acl->deny(null, null, null, new Zend_AclTest_AssertFalse());
        $this->assertTrue($this->_acl->isAllowed());
        $this->_acl->removeDeny();
        $this->assertFalse($this->_acl->isAllowed());
    }

    /**
     * Ensures that removing the default allow rule results in default deny rule being assigned
     *
     * @return void
     */
    public function testRemoveDefaultAllow()
    {
        $this->_acl->allow();
        $this->assertTrue($this->_acl->isAllowed());
        $this->_acl->removeAllow();
        $this->assertFalse($this->_acl->isAllowed());
    }

    /**
     * Ensures that removing non-existent default allow rule does nothing
     *
     * @return void
     */
    public function testRemoveDefaultAllowNonExistent()
    {
        $this->_acl->removeAllow();
        $this->assertFalse($this->_acl->isAllowed());
    }

    /**
     * Ensures that removing non-existent default deny rule does nothing
     *
     * @return void
     */
    public function testRemoveDefaultDenyNonExistent()
    {
        $this->_acl->allow()
                   ->removeDeny();
        $this->assertTrue($this->_acl->isAllowed());
    }

    /**
     * Ensures that for a particular Role, a deny rule on a specific Resource is honored before an allow rule
     * on the entire ACL
     *
     * @return void
     */
    public function testRoleDefaultAllowRuleWithResourceDenyRule()
    {
        $this->_acl->addRole(new Zend_Acl_Role('guest'))
                   ->addRole(new Zend_Acl_Role('staff'), 'guest')
                   ->add(new Zend_Acl_Resource('area1'))
                   ->add(new Zend_Acl_Resource('area2'))
                   ->deny()
                   ->allow('staff')
                   ->deny('staff', array('area1', 'area2'));
        $this->assertFalse($this->_acl->isAllowed('staff', 'area1'));
    }

    /**
     * Ensures that for a particular Role, a deny rule on a specific privilege is honored before an allow
     * rule on the entire ACL
     *
     * @return void
     */
    /**
     * Ensure that basic rule removal works
     *
     * @return void
     */
    public function testRulesRemove()
    {
        $this->_acl->allow(null, null, array('privilege1', 'privilege2'));
        $this->assertFalse($this->_acl->isAllowed());
        $this->assertTrue($this->_acl->isAllowed(null, null, 'privilege1'));
        $this->assertTrue($this->_acl->isAllowed(null, null, 'privilege2'));
        $this->_acl->removeAllow(null, null, 'privilege1');
        $this->assertFalse($this->_acl->isAllowed(null, null, 'privilege1'));
        $this->assertTrue($this->_acl->isAllowed(null, null, 'privilege2'));
    }

    /**
     * Ensures that removal of a Role results in its rules being removed
     *
     * @return void
     */
    public function testRuleRoleRemove()
    {
        $this->_acl->addRole(new Zend_Acl_Role('guest'))
                   ->allow('guest');
        $this->assertTrue($this->_acl->isAllowed('guest'));
        $this->_acl->removeRole('guest');
        try {
            $this->_acl->isAllowed('guest');
            $this->fail('Expected Zend_Acl_Role_Registry_Exception not thrown upon isAllowed() on non-existent Role');
        } catch (Zend_Acl_Role_Registry_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
        $this->_acl->addRole(new Zend_Acl_Role('guest'));
        $this->assertFalse($this->_acl->isAllowed('guest'));
    }

    /**
     * Ensures that removal of all Roles results in Role-specific rules being removed
     *
     * @return void
     */
    public function testRuleRoleRemoveAll()
    {
        $this->_acl->addRole(new Zend_Acl_Role('guest'))
                   ->allow('guest');
        $this->assertTrue($this->_acl->isAllowed('guest'));
        $this->_acl->removeRoleAll();
        try {
            $this->_acl->isAllowed('guest');
            $this->fail('Expected Zend_Acl_Role_Registry_Exception not thrown upon isAllowed() on non-existent Role');
        } catch (Zend_Acl_Role_Registry_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
        $this->_acl->addRole(new Zend_Acl_Role('guest'));
        $this->assertFalse($this->_acl->isAllowed('guest'));
    }

    /**
     * Ensures that removal of a Resource results in its rules being removed
     *
     * @return void
     */
    public function testRulesResourceRemove()
    {
        $this->_acl->add(new Zend_Acl_Resource('area'))
                   ->allow(null, 'area');
        $this->assertTrue($this->_acl->isAllowed(null, 'area'));
        $this->_acl->remove('area');
        try {
            $this->_acl->isAllowed(null, 'area');
            $this->fail('Expected Zend_Acl_Exception not thrown upon isAllowed() on non-existent Resource');
        } catch (Zend_Acl_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
        $this->_acl->add(new Zend_Acl_Resource('area'));
        $this->assertFalse($this->_acl->isAllowed(null, 'area'));
    }

    /**
     * Ensures that removal of all Resources results in Resource-specific rules being removed
     *
     * @return void
     */
    public function testRulesResourceRemoveAll()
    {
        $this->_acl->add(new Zend_Acl_Resource('area'))
                   ->allow(null, 'area');
        $this->assertTrue($this->_acl->isAllowed(null, 'area'));
        $this->_acl->removeAll();
        try {
            $this->_acl->isAllowed(null, 'area');
            $this->fail('Expected Zend_Acl_Exception not thrown upon isAllowed() on non-existent Resource');
        } catch (Zend_Acl_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
        $this->_acl->add(new Zend_Acl_Resource('area'));
        $this->assertFalse($this->_acl->isAllowed(null, 'area'));
    }

    /**
     * Ensures that the $onlyParents argument to inheritsRole() works
     *
     * @return void
     * @see    http://framework.zend.com/issues/browse/ZF-2502
     */
    public function testRoleInheritanceSupportsCheckingOnlyParents()
    {
        $this->_acl->addRole(new Zend_Acl_Role('grandparent'))
                   ->addRole(new Zend_Acl_Role('parent'), 'grandparent')
                   ->addRole(new Zend_Acl_Role('child'), 'parent');
        $this->assertFalse($this->_acl->inheritsRole('child', 'grandparent', true));
    }

}
