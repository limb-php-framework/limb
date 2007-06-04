<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTreeDecoratorTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/tree/src/lmbTreeDecorator.class.php');
lmb_require('limb/tree/src/lmbMPTree.class.php');
lmb_require(dirname(__FILE__) . '/lmbTreeTestBase.class.php');

class TreeTestVersionForDecorator extends lmbMPTree
{
  function __construct()
  {
    parent :: __construct('test_materialized_path_tree');
  }
}

class lmbTreeDecoratorTest extends lmbTreeTestBase
{
  protected $_node_table = 'test_materialized_path_tree';

  function _createTreeImp()
  {
    return new lmbTreeDecorator(new lmbMPTree($this->_node_table, $this->conn,
                                              array('id' => 'id', 'parent_id' => 'p_parent_id',
                                                    'level' => 'p_level', 'identifier' => 'p_identifier',
                                                    'path' => 'p_path')));
  }

  function _cleanUp()
  {
    $this->db->delete($this->_node_table);
  }
}
?>