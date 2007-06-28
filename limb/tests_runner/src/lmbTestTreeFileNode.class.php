<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__). '/lmbTestTreeTerminalNode.class.php');

/**
 * class lmbTestTreeFileNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeFileNode.class.php 6021 2007-06-28 13:18:44Z pachanga $
 */
class lmbTestTreeFileNode extends lmbTestTreeTerminalNode
{
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

  protected function _doCreateTestCase()
  {
    $suite = new TestSuite(basename($this->file));

    if(!is_null($this->class))
    {
      require_once($this->file);
      if(!class_exists($this->class))
        throw new Exception("Class '{$this->class}' not found in '{$this->file}' file!");

      $test = new $this->class();
      $suite->addTestCase($test);
    }
    else
      $suite->addFile($this->file);

    return $suite;
  }
}
?>
