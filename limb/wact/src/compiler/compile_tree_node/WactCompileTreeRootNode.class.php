<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactCompileTreeRootNode.
 *
 * @package wact
 * @version $Id: WactCompileTreeRootNode.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class WactCompileTreeRootNode extends WactCompileTreeNode
{
  function getComponentRefCode()
  {
    return '$root';
  }

  function getDataSource()
  {
    return $this;
  }

  function isDataSource()
  {
    return TRUE;
  }

}
?>