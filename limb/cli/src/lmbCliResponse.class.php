<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbCliResponse.
 *
 * @package cli
 * @version $Id: lmbCliResponse.class.php 7686 2009-03-04 19:57:12Z korchasa $
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


