<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactPHPNode.class.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
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