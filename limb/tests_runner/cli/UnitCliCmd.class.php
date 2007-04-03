<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../src/lmbTestShellUI.class.php');

class UnitCliCmd extends lmbCliBaseCmd
{
  function execute($argv)
  {
    set_time_limit(0);
    error_reporting(E_ALL);

    $ui = new lmbTestShellUI($argv);
    return $ui->runEmbedded();
  }

  function help($argv)
  {
    $ui = new lmbTestShellUI($argv);
    echo $ui->help('unit');
  }
}

?>
