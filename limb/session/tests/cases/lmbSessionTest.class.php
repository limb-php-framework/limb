<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/session/src/lmbSession.class.php');
lmb_require('limb/core/src/lmbObject.class.php');

class lmbSessionTest extends UnitTestCase
{
  protected $session;

  function setUp()
  {
    $this->session = new lmbSession();
  }

  function tearDown()
  {
    $this->session->destroyTouched();
  }

  function testDestroy()
  {
    $key = md5(mt_rand());

    $_SESSION[$key] = 'test';

    $this->session->destroy($key);
    $this->assertFalse($this->session->exists($key));
  }

  function testGet()
  {
    $key = md5(mt_rand());

    $this->assertNull($this->session->get($key));

    $_SESSION[$key] = 'test';

    $this->assertEqual($this->session->get($key), 'test');

    $this->session->destroy($key);
  }

  function testObjectsAreWrappedWithSerialized()
  {
    $object = new lmbObject();

    $this->session->set('some_object', $object);
    $this->assertEqual($this->session->get('some_object'), $object);

    $exported = $this->session->export();
    $this->assertIsA($exported['some_object'], 'lmbSerializable');
    $this->assertEqual($exported['some_object']->getSubject(), $object);
  }

  function testRegisterReference()
  {
    $key = md5(mt_rand());

    $ref =& $this->session->registerReference($key);

    $ref = 'ref test';

    $this->assertEqual($this->session->get($key), 'ref test');
  }

  function testSet()
  {
    $key = md5(mt_rand());

    $this->assertNull($this->session->set($key, $value = 1));
    $this->assertEqual($this->session->get($key), $value);
  }

  function testExists()
  {
    $key = md5(mt_rand());

    $this->assertFalse($this->session->exists($key));

    $_SESSION[$key] = 'test';

    $this->assertTrue($this->session->exists($key));

    $this->session->destroy($key);
  }
}


