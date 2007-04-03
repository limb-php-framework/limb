<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTextNode.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactTextNode extends WactCompileTreeNode
{
  protected $contents;

  function __construct($location, $text)
  {
    parent :: __construct($location);

    $this->contents = $text;
  }

  function generateContents($code_writer)
  {
    $code_writer->writeHTML($this->contents);

    parent :: generateContents($code_writer);
  }
}

?>