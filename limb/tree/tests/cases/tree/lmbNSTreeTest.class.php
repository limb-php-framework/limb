<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMaterializedPathTreeTest.class.php 5677 2007-04-18 14:02:43Z alex433 $
 * @package    tree
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/tree/src/tree/lmbNSTree.class.php');
lmb_require(dirname(__FILE__) . '/lmbTreeTestBase.class.php');

class MSTreeTestVersion extends lmbNSTree
{
  function __construct()
  {
    parent :: __construct('test_nested_sets_tree');
  }
}

class lmbNSTreeTest extends lmbTreeTestBase
{
  function _createTreeImp()
  {
    return new MSTreeTestVersion();
  }

  function _cleanUp()
  {
    $this->db->delete('test_nested_sets_tree');
  }
}
?>
