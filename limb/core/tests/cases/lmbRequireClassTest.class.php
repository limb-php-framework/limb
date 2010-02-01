<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/core/tests/cases/lmbRequireBaseTest.class.php');

class lmbRequireClassTest extends lmbRequireBaseTest
{
  function testLazyLoadClass()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeClassFile($name1, '-blah.php');

    $name2 = $this->_rndName();
    $path2 = $this->_writeClassFile($name2, '-arg.php');

    lmb_require_class($path1, $name1);
    lmb_require_class($path2, $name2);

    $this->assertFalse(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));

    $this->assertTrue(class_exists($name1, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));
  }

  function testLazyLoadSeveralClassesFromOneFile()
  {
    $module = $this->_rndName();

    $name1 = $this->_rndName();
    $name2 = $this->_rndName();
    $path = $this->_writeModule("$module.inc.php", "<?php class $name1 {}; class $name2 {}; ?>");

    lmb_require_class($path, $name1);
    lmb_require_class($path, $name2);

    $this->assertFalse(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));

    $this->assertTrue(class_exists($name2, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_classes()));
    $this->assertTrue(in_array($name2, get_declared_classes()));
  }

  function testLazyLoadClassGuessNameFromFile()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeClassFile($name1, '.php');

    $name2 = $this->_rndName();
    $path2 = $this->_writeClassFile($name2, '.php');

    lmb_require_class($path1);
    lmb_require_class($path2);

    $this->assertFalse(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));

    $this->assertTrue(class_exists($name1, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));
  }

  function testLazyLoadInterface()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeInterfaceFile($name1, '-al.php');
    $name2 = $this->_rndName();
    $path2 = $this->_writeInterfaceFile($name2, '-bl.php');

    lmb_require_class($path1, $name1);
    lmb_require_class($path2, $name2);

    $this->assertFalse(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));

    $this->assertTrue(interface_exists($name1, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));
  }

  function testLazyLoadInterfaceGuessNameFromFile()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeInterfaceFile($name1, '.php');
    $name2 = $this->_rndName();
    $path2 = $this->_writeInterfaceFile($name2, '.php');

    lmb_require_class($path1);
    lmb_require_class($path2);

    $this->assertFalse(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));

    $this->assertTrue(interface_exists($name1, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));
  }

  function testPossibleRecursiveInclude()
  {
    $name = $this->_rndName();
    $path = $this->_writeModule("$name.class.php", "<?php \$foo = new $name(); class $name {} ?>");

    lmb_require_class($path);

    $foo = new $name();
  }

  function testLazyLoadClassConstant()
  {
    $name = $this->_rndName();
    $path = $this->_writeModule($name . '.class.php', "<?php class $name { const Foo = 'value'; }");
    lmb_require($path);
    $this->assertEqual(constant("$name::Foo"), "value");
  }

  /**
   * Identical protection mechanism for both maps (lmb_require and lmb_autoload). For consistency between them
   */
  function testAutoload_mapsConsistency()
  {
    $name = $this->_rndName();
    $path = $this->_writeClassFile($name, '.class.php');

    lmb_require($path);
    $_ENV = array();
    lmb_require($path);
    lmb_autoload($name);
    $this->assertTrue(class_exists($name));
  }
}

