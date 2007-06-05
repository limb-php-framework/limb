<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class lmbCmsTestCase extends UnitTestCase
{
  protected $db;
  protected $toolkit;
  protected $request;
  protected $tree;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
    $this->conn = $this->toolkit->getDefaultDbConnection();
    $this->tree = $this->toolkit->getCmsTree();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
    lmbToolkit :: restore();
  }

  function _cleanUp()
  {
  }

  protected function _initNode($node_identifier, $parent_node = null, $controller_name = 'lmbController')
  {
    $node = new lmbCmsNode();
    $node->setTitle('title_'. mt_rand(0, 10000));
    $node->setIdentifier($node_identifier);
    $node->setControllerName($controller_name);
    if($parent_node)
      $node->setParent($parent_node);
    return $node;
  }

  protected function _createNode($node_identifier, $parent_node = null, $controller_name = 'lmbController')
  {
    $node = $this->_initNode($node_identifier, $parent_node, $controller_name);
    $node->save();
    return $node;
  }
}

?>
