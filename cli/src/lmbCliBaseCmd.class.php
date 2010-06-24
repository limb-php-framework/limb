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
   * @var lmbCliInput
   */
  protected $input;
  /**
   * @var lmbCliResponse
   */
  protected $output;

  function __construct(lmbCliInput $input, lmbCliOutputInterface $output)
  {
    $this->input = $input;
    $this->output = $output;
  }

  abstract function validate();
  abstract function help();
  abstract function execute();

  protected function _error($msg)
  {
    $this->output->error($msg);
    exit(1);
  }
}

