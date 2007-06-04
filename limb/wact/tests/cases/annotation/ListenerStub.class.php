<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: ListenerStub.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class ListenerStub
{
  var $history = array();

  function annotation($param1 = NULL, $param2 = NULL)
  {
    $this->history[] = array('annotation', $param1, $param2);
  }

  function beginClass($param1 = NULL, $param2 = NULL)
  {
    $this->history[] = array('beginClass', $param1, $param2);
  }

  function property($param1 = NULL, $param2 = NULL)
  {
    $this->history[] = array('property', $param1, $param2);
  }

  function method($param1 = NULL)
  {
    $this->history[] = array('method', $param1);
  }

  function endClass()
  {
    $this->history[] = array('endClass');
  }
}

?>
