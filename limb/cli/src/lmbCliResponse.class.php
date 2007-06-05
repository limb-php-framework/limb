<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
class lmbCliResponse
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

  function write($msg)
  {
    if($this->verbose)
      echo $msg;
  }
}

?>
