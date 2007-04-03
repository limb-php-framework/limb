<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsTestCase.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class lmbCmsTestCase extends UnitTestCase
{
  protected $db;
  protected $toolkit;
  protected $request;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
    $this->conn = $this->toolkit->getDefaultDbConnection();
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
