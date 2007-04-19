<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTreeDecoratorTest.class.php 5681 2007-04-19 07:21:57Z pachanga $
 * @package    tree
 */
lmb_require('limb/tree/src/tree/lmbTreeDecorator.class.php');
lmb_require('limb/tree/src/tree/lmbMaterializedPathTree.class.php');

class TreeTestVersionForDecorator extends lmbMaterializedPathTree
{
  function __construct()
  {
    parent :: __construct('test_materialized_path_tree');
  }
}

class lmbTreeDecoratorTest extends lmbMaterializedPathTreeTest
{
  function _createTreeImp()
  {
    return new lmbTreeDecorator(new TreeTestVersionForDecorator());
  }
}
?>