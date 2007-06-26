<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbTestTree.
 *
 * @package tests_runner
 * @version $Id: lmbTestTree.class.php 6016 2007-06-26 13:31:54Z pachanga $
 */
class lmbTestTree
{
  protected $root_node;

  function __construct($root_node)
  {
    $this->root_node = $root_node;
  }

  function find($path)
  {
    $node = $this->root_node->findChildByPath($path);
    $this->root_node->bootstrapPath($path);
    return $node;
  }

  function perform($path, $reporter)
  {
    try
    {
      return $this->_doPerform($path, $reporter);
    }
    catch(Exception $e)
    {
      $this->_showException($e);
      return false;
    }
  }

  protected function _doPerform($path, $reporter)
  {
    if(!$node = $this->find($path))
      throw new Exception("Test node '$path' not found!");

    $test = $node->createTestGroupWithParents();
    $res = $test->run($reporter);

    return $res;
  }

  protected function _showException($e)
  {
    echo $e->__toString();
  }
}

?>