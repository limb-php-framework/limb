<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeGlobNode.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__). '/lmbTestTreeNode.class.php');
require_once(dirname(__FILE__). '/lmbTestTreeDirNode.class.php');

class lmbTestTreeGlobNode extends lmbTestTreeNode
{
  protected $glob;

  function __construct($glob)
  {
    $this->glob = $glob;

    foreach(glob($glob) as $item)
      $this->addChild(new lmbTestTreeDirNode($item));
  }

  function getTestLabel()
  {
    return 'All ' . $this->glob . ' tests ';
  }

  function createTestGroup()
  {
    $group = new GroupTest();
    foreach($this->children as $child)
      $group->addTestCase($child->createTestGroup());
    return $group;
  }

  function createTestGroupWithoutChildren()
  {
    return new GroupTest();
  }
}

?>
