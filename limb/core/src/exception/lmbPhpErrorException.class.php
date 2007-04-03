<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPhpErrorException.class.php 4991 2007-02-08 15:35:35Z pachanga $
 * @package    core
 */
lmb_require(dirname(__FILE__) . '/lmbException.class.php');

class lmbPhpErrorException extends lmbException
{
  function __construct($error_message, $error_number)
  {
    parent :: __construct($error_message, array('error_number' => $error_number));
  }
}
?>