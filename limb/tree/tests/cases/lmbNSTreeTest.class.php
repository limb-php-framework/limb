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

  function testMoveNodeUpDown()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier' => 'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier' => 'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier' => 'node_1_1_1'));
    
    $this->imp->moveNodeUp($node_2);
    
    $moved_node = array(
      'id' => $node_2,
      'parent_id' => $root_id,
      'c_left' => 2,
      'c_right' => 3,
      'level' => 1,
      'identifier' => 'node_2'
    );
    
    $this->assertEqual($this->imp->getNode($node_2)->export(), $moved_node);
    
    $this->imp->moveNodeDown($node_2);
    
    $moved_node = array(
      'id' => $node_2,
      'parent_id' => $root_id,
      'c_left' => 8,
      'c_right' => 9,
      'level' => 1,
      'identifier' => 'node_2'
    );
    
    $this->assertEqual($this->imp->getNode($node_2)->export(), $moved_node);
  }
}
?>
