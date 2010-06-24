<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbTreeInvalidNodeException.
 *
 * @package tree
 * @version $Id$
 */
class lmbTreeInvalidNodeException extends lmbTreeException
{
  protected $node;

  function __construct($node)
  {
    $this->node = $node;
    parent :: __construct("Node '{$node}' is invalid");
  }

  function getNode()
  {
    return $this->node;
  }
}


