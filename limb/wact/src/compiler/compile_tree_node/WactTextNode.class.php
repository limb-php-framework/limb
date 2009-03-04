<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactTextNode.
 *
 * @package wact
 * @version $Id: WactTextNode.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTextNode extends WactCompileTreeNode
{
  protected $contents;

  function __construct($location, $text)
  {
    parent :: __construct($location);

    $this->contents = $text;
  }

  function generate($code_writer)
  {
    $code_writer->writeHTML($this->contents);

    parent :: generate($code_writer);
  }

  function getText()
  {
    return $this->contents;
  }
}


