<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * abstract class lmbTestTreeTerminalNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeTerminalNode.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class lmbTestTreeTerminalNode extends lmbTestTreeNode
{
  function addChild($node){}

  function findChildByPath($path){}

  function isTerminal()
  {
    return true;
  }
}


