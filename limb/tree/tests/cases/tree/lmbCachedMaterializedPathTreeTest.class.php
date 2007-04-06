<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedMaterializedPathTreeTest.class.php 5552 2007-04-06 08:56:27Z pachanga $
 * @package    tree
 */
lmb_require('limb/tree/src/tree/lmbCachingTree.class.php');
lmb_require(dirname(__FILE__) . '/lmbMaterializedPathTreeTest.class.php');

class lmbCachedMaterializedPathTreeTest extends lmbMaterializedPathTreeTest
{
  function _createTreeImp()
  {
    return new lmbCachingTree(new MaterializedPathTreeTestVersion(), LIMB_VAR_DIR . '/tree/');
  }

  function setUp()
  {
    parent :: setUp();
    $this->imp->flushCache();
  }

  function tearDown()
  {
    parent :: tearDown();
    $this->imp->flushCache();
  }
}
?>