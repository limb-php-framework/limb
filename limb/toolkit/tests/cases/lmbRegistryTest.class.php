<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRegistryTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/toolkit/src/lmbRegistry.class.php');

class lmbRegistryTest extends UnitTestCase
{
  function testGetNull()
  {
    $this->assertNull(lmbRegistry :: get('Foo'));
  }

  function testSetGet()
  {
    lmbRegistry :: set('Foo', 'foo');
    $this->assertEqual(lmbRegistry :: get('Foo'), 'foo');
  }

  function testSaveRestore()
  {
    lmbRegistry :: set('Foo', 'foo');

    lmbRegistry :: save('Foo');
    $this->assertEqual(lmbRegistry :: get('Foo'), null);

    lmbRegistry :: set('Foo', 'bar');
    $this->assertEqual(lmbRegistry :: get('Foo'), 'bar');

    lmbRegistry :: save('Foo');
    $this->assertEqual(lmbRegistry :: get('Foo'), null);

    lmbRegistry :: set('Foo', 'baz');
    $this->assertEqual(lmbRegistry :: get('Foo'), 'baz');

    lmbRegistry :: restore('Foo');
    $this->assertEqual(lmbRegistry :: get('Foo'), 'bar');

    lmbRegistry :: restore('Foo');
    $this->assertEqual(lmbRegistry :: get('Foo'), 'foo');
  }

  function testRestoreException()
  {
    try
    {
      lmbRegistry :: restore('No-such');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testSaveException()
  {
    try
    {
      lmbRegistry :: save('No-such');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }
}

?>
