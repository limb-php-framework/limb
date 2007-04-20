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
lmb_require('limb/tree/src/lmbNSTree.class.php');
lmb_require(dirname(__FILE__) . '/lmbTreeTestBase.class.php');

class NSTreeTestVersion extends lmbNSTree
{
  function __construct($node_table)
  {
    parent :: __construct($node_table);
  }
}

class lmbNSTreeTest extends lmbTreeTestBase
{
  protected $_node_table = 'test_nested_sets_tree';

  function _createTreeImp()
  {
    return new NSTreeTestVersion($this->_node_table);
  }

  function _cleanUp()
  {
    $this->db->delete($this->_node_table);
  }
}
?>
