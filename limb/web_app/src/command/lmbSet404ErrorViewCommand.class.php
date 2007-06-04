<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSet404ErrorViewCommand.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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