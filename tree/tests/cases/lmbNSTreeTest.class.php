<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/tree/src/lmbNSTree.class.php');
lmb_require(dirname(__FILE__) . '/lmbTreeTestBase.class.php');

class lmbNSTreeTest extends lmbTreeTestBase
{
  protected $node_table = 'test_nested_sets_tree';

  function _createTreeImp()
  {
    return new lmbNSTree($this->node_table, $this->conn,
                                 array('id' => 'id', 'parent_id' => 'p_parent_id',
                                       'c_left' => 'p_left', 'c_right' => 'p_right',
                                       'level' => 'p_level', 'identifier' => 'p_identifier'));
  }

  function _cleanUp()
  {
    $this->db->delete($this->node_table);
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

