<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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
