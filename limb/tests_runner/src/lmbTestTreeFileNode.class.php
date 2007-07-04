<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__). '/lmbTestTreeTerminalNode.class.php');
require_once(dirname(__FILE__). '/lmbTestUserException.class.php');

@define('LIMB_TESTS_RUNNER_FILE_CLASS_FORMAT', '%s.class.php');

/**
 * class lmbTestTreeFileNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeFileNode.class.php 6064 2007-07-04 08:58:58Z pachanga $
 */
class lmbTestTreeFileNode extends lmbTestTreeTerminalNode
{
  protected static $class_format;
  protected $file;
  protected $class;

  function __construct($file)
  {
    $this->file = $file;
    $this->class = $this->_extractClassName($file);
  }

  static function getClassFormat()
  {
    if(!self :: $class_format)
      self :: $class_format = LIMB_TESTS_RUNNER_FILE_CLASS_FORMAT;
    return self :: $class_format;
  }

  static function setClassFormat($format)
  {
    $prev = self :: $class_format;
    self :: $class_format = $format;
    return $prev;
  }

  function getFile()
  {
    return $this->file;
  }

  function getClass()
  {
    return $this->class;
  }

  protected function _extractClassName($file)
  {
    $regex = preg_quote(self :: $class_format);
    $regex = '~^' . str_replace('%s', '(.*)', $regex) . '$~';

    if(preg_match($regex, basename($file), $m))
      return $m[1];
  }

  protected function _doCreateTestCase()
  {
    $suite = new TestSuite(basename($this->file));

    if(!is_null($this->class))
    {
      require_once($this->file);
      if(!class_exists($this->class))
        throw new lmbTestUserException("Class '{$this->class}' not found in '{$this->file}' file!");

      $test = new $this->class();
      $suite->addTestCase($test);
    }
    else
      $suite->addFile($this->file);

    return $suite;
  }
}
?>
