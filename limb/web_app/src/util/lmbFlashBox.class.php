<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFlashBox.class.php 5431 2007-03-29 15:33:42Z serega $
 * @package    web_app
 */
lmb_require('limb/web_app/src/util/lmbMessageBox.class.php');

class lmbFlashBox extends lmbMessageBox
{
  static function create($session)
  {
    if(!is_object($obj = $session->get(__CLASS__)))
    {
      $obj = new lmbFlashBox();
      $session->set(__CLASS__, $obj);
    }
    return $obj;
  }
}

?>