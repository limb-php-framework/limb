<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroTextNode.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTextNode extends lmbMacroNode
{
  protected static $trim = false;
  protected $contents;

  function __construct($location, $text)
  {
    parent :: __construct($location);
    $this->contents = $text;
  }

  function generate($code_writer)
  {
    if(self :: $trim)
      $code_writer->writeHtml(trim($this->contents));
    else
      $code_writer->writeHtml($this->contents);

    parent :: generate($code_writer);
  }

  static function setTrim($flag = true)
  {
    self :: $trim = $flag;
  }

  function getText()
  {
    return $this->contents;
  }
}


