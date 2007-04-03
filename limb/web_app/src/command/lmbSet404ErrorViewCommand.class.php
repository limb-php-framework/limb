<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSet404ErrorViewCommand.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/command/lmbActionCommand.class.php');

class lmbSet404ErrorViewCommand extends lmbActionCommand
{
  function __construct()
  {
    parent :: __construct('not_found.html');
  }

  function perform()
  {
    $this->resetView();
    parent :: perform();
  }
}

?>