<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliOutputInterface.interface.php');

/**
 * class lmbCliOutput.
 *
 * @package cli
 * @version $Id$
 */
class lmbCliOutput implements lmbCliOutputInterface
{
  protected $verbose = true;

  function __construct($verbose = true)
  {
    $this->verbose = $verbose;
  }

  function setVerbose($verbose)
  {
    $this->verbose = $verbose;
  }

  function error($message, $params = array(), $level = null)
  {
    if($params)
      $message .= var_export($params, true);
    $this->write($message);
  }

  function write($message, $params = array())
  {
    if(!$this->verbose)
      return;

    echo $message;
    if($params)
      $message .= var_export($params, true);
  }
}


