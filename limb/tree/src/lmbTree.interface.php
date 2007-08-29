<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbTree.
 *
 * @package tree
 * @version $Id: lmbTree.interface.php 6243 2007-08-29 11:53:10Z pachanga $
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


