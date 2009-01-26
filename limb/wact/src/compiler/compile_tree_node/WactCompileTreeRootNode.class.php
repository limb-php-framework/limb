<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactCompileTreeRootNode.
 *
 * @package wact
 * @version $Id: WactCompileTreeRootNode.class.php 7486 2009-01-26 19:13:20Z pachanga $
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

