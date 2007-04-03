<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMaterializedPathTreeDecoratorTest.class.php 5008 2007-02-08 15:37:24Z pachanga $
 * @package    tree
 */
lmb_require('limb/tree/src/tree/lmbTreeDecorator.class.php');
lmb_require(dirname(__FILE__) . '/lmbMaterializedPathTreeTest.class.php');

class lmbMaterializedPathTreeDecoratorTest extends lmbMaterializedPathTreeTest
{
  function _createTreeImp()
  {
    return new lmbTreeDecorator(new MaterializedPathTreeTestVersion());
  }
}
?>