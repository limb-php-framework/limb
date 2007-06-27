<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbTestTreePath.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreePath.class.php 6020 2007-06-27 15:12:32Z pachanga $
 */
class lmbTestTreePath
{
  protected $nodes = array();

  function addNode($node)
  {
    $this->nodes[] = $node;
  }

  function createTestGroup()
  {
    if($node = end($this->nodes))
      return $node->createTestGroup();
  }

  function init()
  {
    foreach($this->nodes as $node)
      $node->init();
  }

  function hasSkippedNodes()
  {
    return $this->getSkippedNode() !== null;
  }

  function getSkippedNode()
  {
    foreach($this->nodes as $node)
    {
      if($node->isSkipped())
        return $node;
    }
  }

  function size()
  {
    return count($this->nodes);
  }

  function at($index)
  {
    if(isset($this->nodes[$index]))
      return $this->nodes[$index];
  }

  static function normalize($tests_path)
  {
    return '/' . implode('/', self :: toArray($tests_path));
  }

  static function toArray($tests_path)
  {
    $tests_path = preg_replace('~\/\/+~', '/', $tests_path);
    $tests_path = rtrim($tests_path, '/');
    $path_array = explode('/', $tests_path);

    if(isset($path_array[0]) && $path_array[0] == '')
      array_shift($path_array);

    $new_array = array();
    foreach($path_array as $item)
    {
      if($item == '..')
        array_pop($new_array);
      else
        $new_array[] = $item;
    }
    return $new_array;
  }
}

?>
