<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompileTreeRootNode.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
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