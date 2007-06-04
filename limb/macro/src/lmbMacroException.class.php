<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

class lmbMacroException extends lmbException
{
  function __construct($message, $params = array())
  {
    parent :: __construct('MACRO exception: ' . $message, $params);
  }
}
?>