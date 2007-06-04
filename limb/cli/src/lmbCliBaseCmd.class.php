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
lmb_require('limb/cli/src/lmbCliInput.class.php');

abstract class lmbCliBaseCmd
{
  function __construct($output)
  {
    $this->output = $output;
  }

  function help($argv)
  {
    return 0;
  }

  function execute($argv)
  {
    return 0;
  }

  protected function _error($msg)
  {
    $this->output->write($msg);
    exit(1);
  }
}
?>
