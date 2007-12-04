<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbRequireClassTest extends UnitTestCase
{
  var $tmp_dir;

  function setUp()
  {
    if(!is_dir(LIMB_VAR_DIR))
      mkdir(LIMB_VAR_DIR);

    $this->tmp_dir = LIMB_VAR_DIR . '/lmb_require_class/';
    $this->_rm($this->tmp_dir);
    mkdir($this->tmp_dir);
  }

  function tearDown()
  {
    $this->_rm($this->tmp_dir);
  }

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

  function _writeClassFile($name, $ext = '.class.php')
  {
    $path = $this->tmp_dir . $name . $ext;
    $this->_write($path, $this->_classCode($name));
    return $path;
  }

  function _writeInterfaceFile($name, $ext = '.interface.php')
  {
    $path = $this->tmp_dir . $name . $ext;
    $this->_write($path, $this->_faceCode($name));
    return $path;
  }

  function _writeModule($name, $contents)
  {
    $path = $this->tmp_dir . $name;
    $this->_write($path, $contents);
    return $path;
  }

  function _classCode($name)
  {
    return "<?php class $name {} ?>";
  }

  function _faceCode($name)
  {
    return "<?php interface $name {} ?>";
  }

  function _rndName()
  {
    return 'Foo' . mt_rand(1, 1000);
  }

  function _rnd()
  {
    return mt_rand(1, 1000);
  }

  function _write($file, $contents='')
  {
    file_put_contents($file, $contents);
  }

  function _rm($path)
  {
    if(!is_dir($path))
      return;
    $dir = opendir($path);
    while($entry = readdir($dir))
    {
     if(is_file("$path/$entry"))
       unlink("$path/$entry");
     elseif(is_dir("$path/$entry") && $entry != '.' && $entry != '..')
       $this->_rm("$path/$entry");
    }
    closedir($dir);
    return rmdir($path);
  }
}

