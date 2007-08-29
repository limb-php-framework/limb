<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/util/lmbMessageBox.class.php');

/**
 * class lmbFlashBox.
 *
 * @package web_app
 * @version $Id: lmbFlashBox.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
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


