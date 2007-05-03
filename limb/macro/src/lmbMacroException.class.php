<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: error.inc.php 5334 2007-03-23 11:48:20Z pachanga $
 * @package    macro
 */

class lmbMacroException extends lmbException
{
  function __construct($message, $params = array())
  {
    parent :: __construct('MACRO exception: ' . $message, $params);
  }
}
?>