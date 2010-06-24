<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/core/tests/cases/lmbRequireBaseTest.class.php');

class lmbRequireTest extends lmbRequireBaseTest
{
  function testLazyLoadClass()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeClassFile($name1, '.php');

    $name2 = $this->_rndName();
    $path2 = $this->_writeClassFile($name2, '.php');

    lmb_require($path1, $name1);
    lmb_require($path2, $name2);

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

    lmb_require($path, $name1);
    lmb_require($path, $name2);

    $this->assertFalse(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));

    $this->assertTrue(class_exists($name2, true));//triggers autoload, we try 2nd class first since we need to check how behaves static $tried cache

    $this->assertTrue(in_array($name1, get_declared_classes()));
    $this->assertTrue(in_array($name2, get_declared_classes()));
  }

  function testLazyLoadClassGuessNameFromFile()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeClassFile($name1, '.class.php');

    $name2 = $this->_rndName();
    $path2 = $this->_writeClassFile($name2, '.class.php');

    lmb_require($path1);
    lmb_require($path2);

    $this->assertFalse(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));

    $this->assertTrue(class_exists($name1, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_classes()));
    $this->assertFalse(in_array($name2, get_declared_classes()));
  }

  function testLazyLoadInterface()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeInterfaceFile($name1, '.php');
    $name2 = $this->_rndName();
    $path2 = $this->_writeInterfaceFile($name2, '.php');

    lmb_require($path1, $name1);
    lmb_require($path2, $name2);

    $this->assertFalse(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));

    $this->assertTrue(interface_exists($name1, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));
  }

  function testLazyLoadInterfaceGuessNameFromFile()
  {
    $name1 = $this->_rndName();
    $path1 = $this->_writeInterfaceFile($name1, '.interface.php');
    $name2 = $this->_rndName();
    $path2 = $this->_writeInterfaceFile($name2, '.interface.php');

    lmb_require($path1);
    lmb_require($path2);

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

    lmb_require($path);

    $foo = new $name();
  }

  function testNoLazyLoadForModule()
  {
    $name = $this->_rndName();
    $path = $this->_writeModule("$name.inc.php", "<?php class $name {} ?>");

    lmb_require($path);
    $this->assertTrue(in_array($name, get_declared_classes()));
  }

  function testGlobRequireWithAutoload()
  {
    //creating new unique directory
    $old_dir = $this->tmp_dir;
    $this->tmp_dir = $this->tmp_dir . $this->_rndName() . '/';
    mkdir($this->tmp_dir);

    $c1 = $this->_rndName();
    $c2 = $this->_rndName();
    $c3 = $this->_rndName();

    $path1 = $this->_writeClassFile($c1);
    $path2 = $this->_writeClassFile($c2);
    $path3 = $this->_writeClassFile($c3);

    lmb_require($this->tmp_dir . '/*.class.php');

    foreach(array($c1, $c2, $c3) as $c)
    {
      $this->assertFalse(in_array($c, get_declared_interfaces()));
      $this->assertTrue(class_exists($c, true));
    }

    $this->tmp_dir = $old_dir;
  }

  function testGlobRequireWithAutoload2()
  {
    //creating new unique directory
    $old_dir = $this->tmp_dir;
    $this->tmp_dir = $this->tmp_dir . $this->_rndName() . '/';
    mkdir($this->tmp_dir);

    $c1 = $this->_rndName();
    $c2 = $this->_rndName();
    $c3 = $this->_rndName();

    $path1 = $this->_writeClassFile($c1);
    $path2 = $this->_writeClassFile($c2);
    $path3 = $this->_writeClassFile($c3);

    lmb_require_glob($this->tmp_dir . '/*.class.php');

    foreach(array($c1, $c2, $c3) as $c)
    {
      $this->assertFalse(in_array($c, get_declared_interfaces()));
      $this->assertTrue(class_exists($c, true));
    }

    $this->tmp_dir = $old_dir;
  }

  function testRequireFileCacheHit()
  {
    if(!function_exists('xdebug_start_code_coverage'))
      return;

    //we need to prevent collisions with external code coverage analyzing tools
    if(xdebug_get_code_coverage())
      return;

    $name = $this->_rndName();
    $path = $this->_writeModule("$name.inc.php", "<?php class $name {} ?>");

    $func = new ReflectionFunction('lmb_require');
    $file = $func->getFileName();

    $line = $this->_locateIncludeOnceLine($file,  $func->getStartLine());

    xdebug_start_code_coverage();
    lmb_require($path);
    $cov1 = xdebug_get_code_coverage();
    xdebug_stop_code_coverage();

    xdebug_start_code_coverage();
    lmb_require($path);
    $cov2 = xdebug_get_code_coverage();
    xdebug_stop_code_coverage();

    $this->assertEqual($cov1[$file][$line], 1);
    $this->assertFalse(isset($cov2[$file][$line]));
  }

  function testRequireThrowsExceptionForNonExistingFile()
  {
    try
    {
      @lmb_require($file = 'foo_' . mt_rand() . uniqid() . '.inc.php');
    }
    catch(lmbException $e)
    {
      $this->assertPattern('~' . preg_quote($file) . '~', $e->getMessage());
    }
  }

  function testRequireOptionalOk()
  {
    $name = $this->_rndName();
    $path = $this->_writeModule("$name.class.php", "<?php class $name {} ?>");

    lmb_require_optional($path);

    $foo = new $name();
  }

  function testRequireOptionalDoesntThrowExceptionForNonExistingFile()
  {
    lmb_require_optional($file = 'foo_' . mt_rand() . uniqid() . '.inc.php');
  }

  function testRequireOptionalGlobDoesntThrowExceptionForNonExistingFiles()
  {
    lmb_require_optional($file = 'foo_' . mt_rand() . uniqid() . '*.php');
  }
}

