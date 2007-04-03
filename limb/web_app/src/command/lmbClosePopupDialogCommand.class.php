<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbClosePopupDialogCommand.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/command/lmbActionCommand.class.php');

class lmbClosePopupDialogCommand extends lmbActionCommand
{
  function __construct()
  {
    parent :: __construct('close_popup.html');
  }

  function perform()
  {
    $this->resetView();
    parent :: perform();
  }
}


?>
