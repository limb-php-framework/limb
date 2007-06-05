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

  function generate($add_php = true)
  {
    $code = '';
    $code .= $add_php ? "<?php\n" : '';
    $code .= "class {$this->class_name} extends UnitTestCase {
              function testMe() {echo \"{$this->class_name}\";}
            }";
    $code .= $add_php ? "\n?>" : '';

    return $code;
  }

  function generateBareBoned()
  {
    return $this->generate(false);
  }
}

abstract class lmbTestsUtilitiesBase extends UnitTestCase
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
}
?>