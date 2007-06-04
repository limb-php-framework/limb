<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeTerminalNode.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

abstract class lmbTestTreeTerminalNode extends lmbTestTreeNode
{
  function addChild($node){}
  function findChildByPath($path){}
  function createTestGroupWithoutChildren()
  {
    return $this->createTestGroup();
  }

  function isTerminal()
  {
    return true;
  }
}

?>
