<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroSourceLocation.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroSourceLocation
{
  public $file;
  public $line;

  function __construct($file = null, $line = null)
  {
    if($file)
      $this->file = $file;
    else
      $this->file = 'unknown file';

    if($line)
      $this->line = $line;
    else
      $this->line = 'unknown line';
  }

  function getFile()
  {
    return $this->file;
  }

  function getLine()
  {
    return $this->line;
  }
}

