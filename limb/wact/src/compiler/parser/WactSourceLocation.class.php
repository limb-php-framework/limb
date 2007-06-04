<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactSourceLocation.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class WactSourceLocation
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
?>