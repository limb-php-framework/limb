<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/web_agent/src/lmbWebServerCookie.class.php');

/**
 * @package web_agent
 * @version $Id: lmbWebServerCookieTest.class.php 39 2007-10-03 21:08:36Z CatMan $
 */
class lmbWebServerCookieTest extends UnitTestCase {

  function testCookie()
  {
    $cookie = new lmbWebServerCookie('sid=xzxsd; expires=date; path=/; domain=.test.ru; secure');

    $this->assertEqual($cookie->name, 'sid');
    $this->assertEqual($cookie->value, 'xzxsd');
    $this->assertEqual($cookie->expires, 'date');
    $this->assertEqual($cookie->path, '/');
    $this->assertEqual($cookie->domain, '.test.ru');
    $this->assertTrue($cookie->secure);
  }

}
