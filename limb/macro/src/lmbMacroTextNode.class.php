<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMacroTextNode.class.php 5780 2007-04-28 13:03:26Z serega $
 * @package    macro
 */

class lmbMacroTextNode extends lmbMacroNode
{
  protected $contents;

  function __construct($location, $text)
  {
    parent :: __construct($location);
    $this->contents = $text;
  }

  function generateContents($code_writer)
  {
    $code_writer->writeRaw($this->contents);
    parent :: generateContents($code_writer);
  }

  function getText()
  {
    return $this->contents;
  }
}

?>