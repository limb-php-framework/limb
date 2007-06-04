<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTree.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

interface lmbTree
{
  function initTree();
  function isNode($node);
  function getNode($node);
  function getRootNode();
  function getParent($node);
  function getParents($node);
  function getSiblings($node);
  function getChildren($node, $depth = 1);
  function getChildrenAll($node);
  function countChildren($node, $depth = 1);
  function countChildrenAll($node);
  function createNode($parent_node, $values);
  function deleteNode($node);
  function deleteAll();
  function updateNode($node, $values, $internal = false);
  function moveNode($source_node, $target_node);
  function getNodesByIds($ids_array);
  function getNodeByPath($path);
  function getPathToNode($node);
}

?>