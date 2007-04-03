<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebAppErrorList.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/validation/src/lmbErrorList.class.php');

class lmbWebAppErrorList extends lmbErrorList
{
  function addError($message, $fields = array(), $values = array())
  {
    $error = parent :: addError($message, $fields, $values);

    lmbToolkit :: instance()->getMessageBox()->addError($error);
  }
}
