<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroTemplateExecutor.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateExecutor
{
  function __construct($vars)
  {
    foreach($vars as $name => $value)
      $this->name = $value;
  }

  function set($name, $value)
  {
    $this->$name = $value;
  }

  function render(){}
}

