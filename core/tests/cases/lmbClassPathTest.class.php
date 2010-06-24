<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbClassPath.class.php');

class TestStubForClassPathTest
{
  var $a;
  var $b;

  function __construct($a=null, $b=null)
  {
    $this->a = $a;
    $this->b = $b;
  }
}

class lmbClassPathTest extends UnitTestCase
{
  function setUp()
  {
    if(!is_dir(LIMB_VAR_DIR))
      mkdir(LIMB_VAR_DIR);
  }

  function testInvalidClassPath()
  {
    try
    {
      $class_path = new lmbClassPath(null);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    try
    {
      $class_path = new lmbClassPath(false);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    try
    {
      $class_path = new lmbClassPath(3);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    //Do we need this behaviour? Is it an unneccessary overhead to check for this?
    /*try
    {
      $class_path = new lmbClassPath('invalid.class.path');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}*/
  }

  function testGetClassName()
  {
    $class_path = new lmbClassPath('/foo/Bar');
    $this->assertEqual($class_path->getClassName(), 'Bar');

    $class_path = new lmbClassPath('Bar');
    $this->assertEqual($class_path->getClassName(), 'Bar');
  }

  function testCreateHandle()
  {
    $class_path = new lmbClassPath('TestStubForClassPathTest');
    $handle = $class_path->createHandle();

    $this->assertEqual(get_class($handle), 'lmbHandle');
    $this->assertEqual(get_class($handle->resolve()), 'TestStubForClassPathTest');
  }

  function testCreateHandlePassArgs()
  {
    $class_path = new lmbClassPath('TestStubForClassPathTest');
    $handle = $class_path->createHandle(array(1, 2));

    $this->assertEqual(get_class($handle), 'lmbHandle');
    $this->assertEqual($handle->a, 1);
    $this->assertEqual($handle->b, 2);
  }

  function testCreateObjectRequireClass()
  {
    file_put_contents(LIMB_VAR_DIR . '/FooBarZooTest.class.php', "<?php\n class FooBarZooTest{}\n ?>");

    $class_path = new lmbClassPath(LIMB_VAR_DIR . '/FooBarZooTest');
    $this->assertEqual(get_class($class_path->createObject()), 'FooBarZooTest');

    unlink(LIMB_VAR_DIR . '/FooBarZooTest.class.php');
  }

  function testImport()
  {
    file_put_contents(LIMB_VAR_DIR . '/FooBarZooTest2.class.php', "<?php\n class FooBarZooTest2{}\n ?>");

    $class_path = new lmbClassPath(LIMB_VAR_DIR . '/FooBarZooTest2');

    $this->assertFalse(class_exists('FooBarZooTest2', true));
    $class_path->import();
    $this->assertTrue(class_exists('FooBarZooTest2', true));

    unlink(LIMB_VAR_DIR . '/FooBarZooTest2.class.php');
  }

  function testImportShortNamedClassFromNonExistingFile()
  {
    $class_path = new lmbClassPath('FooBarHeyZoo');

    try
    {
      $class_path->import();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testImportShortNamedClass()
  {
    file_put_contents(LIMB_VAR_DIR . '/FooBarZooTest3.class.php', "<?php\n class FooBarZooTest3{}\n ?>");

    require_once(LIMB_VAR_DIR . '/FooBarZooTest3.class.php');

    $class_path = new lmbClassPath('FooBarZooTest3');
    $class_path->import();

    unlink(LIMB_VAR_DIR . '/FooBarZooTest3.class.php');
  }

  function testCreateObjectNoArgs()
  {
    $class_path = new lmbClassPath('TestStubForClassPathTest');
    $obj = $class_path->createObject();

    $this->assertEqual(get_class($obj), 'TestStubForClassPathTest');
  }

  function testCreateObjectPassArgs()
  {
    $class_path = new lmbClassPath('TestStubForClassPathTest');
    $obj = $class_path->createObject(array(1, 2));

    $this->assertEqual(get_class($obj), 'TestStubForClassPathTest');
    $this->assertEqual($obj->a, 1);
    $this->assertEqual($obj->b, 2);
  }

  function testCreateStaticCall()
  {
    $obj = lmbClassPath :: create('TestStubForClassPathTest', array(1, 2));
    $this->assertEqual(get_class($obj), 'TestStubForClassPathTest');
    $this->assertEqual($obj->a, 1);
    $this->assertEqual($obj->b, 2);
  }

  function testExpandConstants()
  {
    file_put_contents(LIMB_VAR_DIR . '/FooBarZooTest2.class.php', "<?php\n class FooBarZooTest2{}\n ?>");

    $class_path = new lmbClassPath('{LIMB_VAR_DIR}/FooBarZooTest2');
    $this->assertEqual(get_class($class_path->createObject()), 'FooBarZooTest2');

    unlink(LIMB_VAR_DIR . '/FooBarZooTest2.class.php');
  }
}

