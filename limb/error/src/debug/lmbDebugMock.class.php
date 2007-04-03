<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDebugMock.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */

Mock :: generate('lmbDebug', 'lmbPreDebugMock');

class lmbDebugMock extends lmbPreDebugMock
{
  //SimpleTest mock object interface
  function atTestEnd($method)
  {
    parent :: atTestEnd($method);
    $debug = lmbDebug :: instance();
    $debug->resetExpectations();
  }

  function getNextTiming()
  {
    return sizeof($this->_expected_args_at) - 1;
  }
}

?>
