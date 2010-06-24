<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroException.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroException extends lmbException
{
  function __construct($message, $params = array())
  {
    parent :: __construct('MACRO exception: ' . $message, $params);
  }
}

