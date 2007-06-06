<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactPHPNode.
 *
 * @package wact
 * @version $Id: WactPHPNode.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class WactPHPNode extends WactCompileTreeNode
{
  protected $contents;

  function __construct($location, $text)
  {
    parent :: __construct($location);

    $this->contents = $text;
  }

  function generate($code_writer)
  {
    $code_writer->writePHP($this->contents);
  }

  function getCode()
  {
    return $this->contents;
  }
}
?>