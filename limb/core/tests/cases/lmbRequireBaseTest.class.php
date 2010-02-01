<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

abstract class lmbRequireBaseTest extends UnitTestCase
{
  var $tmp_dir;
  var $env;

  function setUp()
  {
    if(!is_dir(lmb_var_dir()))
      mkdir(lmb_var_dir());

    $this->tmp_dir = lmb_var_dir() . '/lmb_require/';
    $this->_rm($this->tmp_dir);
    mkdir($this->tmp_dir);
    $this->env = $_ENV;
  }

  function tearDown()
  {
    $this->_rm($this->tmp_dir);
    $_ENV = $this->env;
  }

  protected function _locateIncludeOnceLine($file, $start_line)
  {
    $c = 0;
    foreach(file($file) as $line)
    {
      $c++;
      if($c >= $start_line && strpos($line, 'include_once') !== false)
        return $c;
    }
  }

  protected function _writeClassFile($name, $ext = '.class.php')
  {
    $path = $this->tmp_dir . $name . $ext;
    $this->_write($path, $this->_classCode($name));
    return $path;
  }

  protected function _writeInterfaceFile($name, $ext = '.interface.php')
  {
    $path = $this->tmp_dir . $name . $ext;
    $this->_write($path, $this->_faceCode($name));
    return $path;
  }

  protected function _writeModule($name, $contents)
  {
    $path = $this->tmp_dir . $name;
    $this->_write($path, $contents);
    return $path;
  }

  protected function _classCode($name)
  {
    return "<?php class $name {} ?>";
  }

  protected function _faceCode($name)
  {
    return "<?php interface $name {} ?>";
  }

  protected function _rndName()
  {
    return 'Foo' . md5(microtime());
  }

  protected function _rnd()
  {
    return mt_rand(1, 1000) . uniqid();
  }

  protected function _write($file, $contents='')
  {
    file_put_contents($file, $contents);
  }

  protected function _rm($path)
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

