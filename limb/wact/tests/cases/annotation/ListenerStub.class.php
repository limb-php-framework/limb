<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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


