<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequireTest.class.php 4991 2007-02-08 15:35:35Z pachanga $
 * @package    core
 */

class lmbRequireTest extends UnitTestCase
{
  var $tmp_dir;

  function setUp()
  {
    if(!is_dir(LIMB_VAR_DIR))
      mkdir(LIMB_VAR_DIR);

    $this->tmp_dir = LIMB_VAR_DIR . '/lmb_require/';
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
    $path1 = $this->_writeClassFile($name1);

    $name2 = $this->_rndName();
    $path2 = $this->_writeClassFile($name2);

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
    $path1 = $this->_writeInterfaceFile($name1);
    $name2 = $this->_rndName();
    $path2 = $this->_writeInterfaceFile($name2);

    lmb_require($path1);
    lmb_require($path2);

    $this->assertFalse(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));

    $this->assertTrue(interface_exists($name1, true));//triggers autoload

    $this->assertTrue(in_array($name1, get_declared_interfaces()));
    $this->assertFalse(in_array($name2, get_declared_interfaces()));
  }

  function testRecursionProtection()
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

  function testRequireFileCacheHit()
  {
    if(!function_exists('xdebug_start_code_coverage'))
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

  function _locateIncludeOnceLine($file, $start_line)
  {
    $c = 0;
    foreach(file($file) as $line)
    {
      $c++;
      if($c >= $start_line && strpos($line, 'include_once') !== false)
        return $c;
    }
  }

  function _writeClassFile($name, $body = null)
  {
    $path = $this->tmp_dir . $name . '.class.php';
    $this->_write($path, $body ? $body : $this->_classCode($name));
    return $path;
  }

  function _writeInterfaceFile($name)
  {
    $path = $this->tmp_dir . $name . '.interface.php';
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
?>