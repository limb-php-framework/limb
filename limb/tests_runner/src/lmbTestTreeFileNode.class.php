<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeFileNode.class.php 5157 2007-02-26 13:59:16Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__). '/lmbTestTreeTerminalNode.class.php');

class lmbTestTreeFileNode extends lmbTestTreeTerminalNode
{
  protected $test_group;
  protected $file;
  protected $class;

  function __construct($file, $class = null)
  {
    $this->file = $file;
    $this->class = $class;
  }

  function getFile()
  {
    return $this->file;
  }

  function getClass()
  {
    return $this->class;
  }

  function createTestGroup()
  {
    if($this->test_group)
      return $this->test_group;

    $this->test_group = new GroupTest(basename($this->file));

    if(!is_null($this->class))
    {
      require_once($this->file);
      if(!class_exists($this->class))
        throw new Exception("Class '{$this->class}' not found in '{$this->file}' file!");

      $test = new $this->class();
      $this->test_group->addTestCase($test);
    }
    else
      $this->test_group->addTestFileOnce($this->file);

    return $this->test_group;
  }
}
?>
