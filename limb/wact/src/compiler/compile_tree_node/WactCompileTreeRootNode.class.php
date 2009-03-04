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
 * @version $Id: WactCompileTreeRootNode.class.php 7686 2009-03-04 19:57:12Z korchasa $
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

