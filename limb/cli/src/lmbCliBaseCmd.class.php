<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliInput.class.php');

/**
 * abstract class lmbCliBaseCmd.
 *
 * @package cli
 * @version $Id$
 */
abstract class lmbCliBaseCmd
{
  /**
   * @var lmbCliResponse
   */
  protected $output;

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

