<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/acl/src/lmbAcl.class.php');

class lmbAclAllowsAcceptanceTest extends UnitTestCase  
{
  function testAcceptance()
  {
    $acl = new lmbAcl();

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
