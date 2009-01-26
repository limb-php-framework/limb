<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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


