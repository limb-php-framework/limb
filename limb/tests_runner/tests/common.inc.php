<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class GeneratedTestClass
{
  protected $class_name;

  function __construct()
  {
    $this->class_name = 'GenClass_' . mt_rand(1, 10000);
  }

  function getClass()
  {
    return $this->class_name;
  }

  function getFileName()
  {
    return $this->class_name . ".class.php";
  }

  function getOutput()
  {
    return $this->class_name . "\n";
  }

  function generate()
  {
    $code = '';
    $code .= "<?php\n";
    $code .= $this->generateClass();
    $code .= "\n?>";
    return $code;
  }

  function generateFailing()
  {
    $code = '';
    $code .= "<?php\n";
    $code .= $this->generateClassFailing();
    $code .= "\n?>";
    return $code;
  }

  function generateClass()
  {
    $code = "class {$this->class_name} extends UnitTestCase {
              function testSay() {echo \"" . $this->getOutput() . "\";}
            }";
    return $code;
  }

  function generateClassFailing()
  {
    $code = "class {$this->class_name} extends UnitTestCase {
              function testSay() {\$this->assertTrue(false);echo \"" . $this->getOutput() . "\";}
            }";
    return $code;
  }
}

abstract class lmbTestRunnerBase extends UnitTestCase
{
  function _rmdir($path)
  {
    if(!is_dir($path))
      return;

    $dir = opendir($path);
    while($entry = readdir($dir))
    {
      if(is_file("$path/$entry"))
        unlink("$path/$entry");
      elseif(is_dir("$path/$entry") && $entry != '.' && $entry != '..')
        $this->_rmdir("$path/$entry");
    }
    closedir($dir);
    $res = rmdir($path);
    clearstatcache();
    return $res;
  }

  function _createTestCase($file, $extra = '')
  {
    $dir = dirname($file);
    if(!is_dir($dir))
      mkdir($dir, 0777, true);

    $generated = new GeneratedTestClass();
    file_put_contents($file, "<?php\n" . $generated->generateClass() . $extra . "\n?>");
    return $generated;
  }

  function _createTestCaseFailing($file, $extra = '')
  {
    $dir = dirname($file);
    if(!is_dir($dir))
      mkdir($dir, 0777, true);

    $generated = new GeneratedTestClass();
    file_put_contents($file, "<?php\n" . $generated->generateClassFailing() . $extra . "\n?>");
    return $generated;
  }
}
?>