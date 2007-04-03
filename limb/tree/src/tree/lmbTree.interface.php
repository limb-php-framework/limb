<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTree.interface.php 5008 2007-02-08 15:37:24Z pachanga $
 * @package    tree
 */

interface lmbTree
{
  function isNode($id);
  function getNode($id);
  function getParent($id);
  function getParents($id);
  function getSiblings($id);
  function getChildren($id);
  function countChildren($id);
  function createNode($values, $parent_node = null);
  function createRootNode($values);
  function createSubNode($id, $values);
  function deleteNode($id);
  function updateNode($id, $values, $internal = false);
  function moveTree($id, $target_id);
  function setDumbMode($status=true);
  function getNodesByIds($ids_array);
  function getMaxChildIdentifier($id);
  function getNodeByPath($path, $delimiter='/');
  function getPathToNode($node, $delimeter = '/');
  function getSubBranch($id, $depth = -1, $include_parent = false);
  function getSubBranchByPath($path, $depth = -1, $include_parent = false);
  function getRootNodes();
}

?>