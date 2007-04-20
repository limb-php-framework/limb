<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTree.interface.php 5694 2007-04-19 15:19:07Z pachanga $
 * @package    tree
 */

class lmbInvalidNodeTreeException extends lmbTreeException
{
  protected $node;

  function __construct($node)
  {
    $this->node = $node;
    parent :: __construct("Node '$node' is invalid");
  }

  function getNode()
  {
    return $this->node;
  }
}

?>