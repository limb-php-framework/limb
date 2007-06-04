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
require_once(dirname(__FILE__) . '/../src/lmbTestShellUI.class.php');

class UnitCliCmd extends lmbCliBaseCmd
{
  function execute($argv)
  {
    set_time_limit(0);
    error_reporting(E_ALL);

    $ui = new lmbTestShellUI($argv);
    $ui->setPosixMode(false);
    return ($ui->runEmbedded() ? 0 : 1);
  }

  function help($argv)
  {
    $ui = new lmbTestShellUI($argv);
    echo $ui->help('unit');
  }
}

?>
