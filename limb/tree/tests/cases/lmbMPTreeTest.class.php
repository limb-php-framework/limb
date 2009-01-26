<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/tree/src/lmbMPTree.class.php');
lmb_require(dirname(__FILE__) . '/lmbTreeTestBase.class.php');


class lmbMPTreeTest extends lmbTreeTestBase
{
  protected $node_table = 'test_materialized_path_tree';

  function _createTreeImp()
  {
    return new lmbMPTree($this->node_table, $this->conn,
                         array('id' => 'id', 'parent_id' => 'p_parent_id',
                               'level' => 'p_level', 'identifier' => 'p_identifier',
                               'path' => 'p_path'));
  }

  function _cleanUp()
  {
    $this->db->delete($this->node_table);
  }

  function _checkProperNesting($nodes, $line='')
  {
    $this->assertEqual(lmbArrayHelper :: sortArray($nodes, array('path' => 'ASC')),
                       $nodes);

    $path = lmbArrayHelper :: getMinColumnValue('path', $nodes, $index);
    $parent_paths[] = $this->_getParentPath($path);

    $counter = 0;
    foreach($nodes as $id => $node)
    {
      $parent_path = $this->_getParentPath($node['path']);

      $this->assertTrue(in_array($parent_path, $parent_paths),
        'path is improperly nested: ' . $node['path'] . ' , expected parent not found: ' . $parent_path . ' at line: ' . $line);

      $parent_paths[] = $node['path'];
    }
  }

  function _getParentPath($path)
  {
    preg_match('~^(.*/)[^/]+/$~', $path, $matches);
    return $matches[1];
  }

  function testGetChildrenAllowOtherSort()
  {
    $root_id = $this->imp->initTree();

    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2_1 = $this->imp->createNode($node_1, array('identifier'=>'aaaaa'));
    $node_2_2 = $this->imp->createNode($node_1, array('identifier'=>'ccccc'));
    $node_2_3 = $this->imp->createNode($node_1, array('identifier'=>'bbbb'));

    $rs = $this->imp->getChildren($node_1);
    $arr = $rs->sort(array('p_identifier' => 'DESC'))->getArray();

    $this->assertEqual(sizeof($arr ), 3);
    $this->assertEqual($arr[0]['id'], $node_2_2);
    $this->assertEqual($arr[1]['id'], $node_2_3);
    $this->assertEqual($arr[2]['id'], $node_2_1);

    $rs = $this->imp->getChildren($node_1);
    $arr = $rs->sort(array('p_identifier' => 'ASC'))->getArray();

    $this->assertEqual($arr[0]['id'], $node_2_1);
    $this->assertEqual($arr[1]['id'], $node_2_3);
    $this->assertEqual($arr[2]['id'], $node_2_2);
  }
}

