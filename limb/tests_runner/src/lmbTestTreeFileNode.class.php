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

/**
 * class lmbTestTreeFileNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeFileNode.class.php 6218 2007-08-06 12:16:32Z pachanga $
 */
class lmbTestTreeFileNode extends lmbTestTreeTerminalNode
{
  protected $file;

  function __construct($file)
  {
    $this->file = $file;
  }

  function getFile()
  {
    return $this->file;
  }

  protected function _getClassesDefinedInFile()
  {
     if(!preg_match_all('~\Wclass\s+(\w+)~', file_get_contents($this->file), $matches))
       return array();
     return $matches[1];
  }

  protected function _doCreateTestCase()
  {
    require_once($this->file);
    $suite = new lmbTestGroup(basename($this->file));
    $candidates = $this->_getClassesDefinedInFile();
    $loader = new SimpleFileLoader();
    foreach($loader->selectRunnableTests($candidates) as $class)
      $suite->addTestCase(new $class());

    return $suite;
  }
}
?>
