<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/tree/src/lmbTree.interface.php');
lmb_require('limb/tree/src/exception/lmbTreeException.class.php');
lmb_require('limb/tree/src/exception/lmbTreeInvalidNodeException.class.php');
lmb_require('limb/tree/src/exception/lmbTreeConsistencyException.class.php');

/**
 * Base class implementing a Nested Sets approach for storing tree-like structures in database tables.
 * @package tree
 * @version $Id$
 */
class lmbNSTree implements lmbTree
{
  protected $_conn;

  protected $_system_columns = array();
  protected $_column_map;

  protected $_id;
  protected $_parent_id;
  protected $_left;
  protected $_right;
  protected $_identifier;
  protected $_level;

  protected $_db_table;

  function __construct($node_table = 'ns_tree',
                       $conn = null,
                       $column_map = array('id' => 'id', 'parent_id' => 'parent_id',
                                           'c_left' => 'c_left', 'c_right' => 'c_right',
                                           'level' => 'level', 'identifier' => 'identifier'
                                           ))
  {
    $this->_mapColumns($column_map);

    if($conn)
      $this->_conn = $conn;
    else
      $this->_conn = lmbToolkit :: instance()->getDefaultDbConnection();

    $this->_node_table = $node_table;
    $this->_db_table = new lmbTableGateway($this->_node_table, $this->_conn);
  }

  protected function _mapColumns($column_map)
  {
    $this->_id = isset($column_map['id']) ? $column_map['id'] : 'id';
    $this->_parent_id = isset($column_map['parent_id']) ? $column_map['parent_id'] : 'parent_id';
    $this->_left = isset($column_map['c_left']) ? $column_map['c_left'] : 'c_left';
    $this->_right = isset($column_map['c_right']) ? $column_map['c_right'] : 'c_right';
    $this->_level = isset($column_map['level']) ? $column_map['level'] : 'level';
    $this->_identifier = isset($column_map['identifier']) ? $column_map['identifier'] : 'identifier';

    $this->_system_columns = array($this->_id, $this->_parent_id, $this->_left,
                                  $this->_right, $this->_level);

    $this->_column_map = array('id' => $this->_id, 'parent_id' => $this->_parent_id,
                               'level' => $this->_level, 'identifier' => $this->_identifier,
                               'c_left' => $this->_left, 'c_right' => $this->_right);
  }

  function setNodeTable($table_name)
  {
    $this->_node_table = $table_name;
  }

  function getNodeTable()
  {
    return $this->_node_table;
  }

  function _getSelectFields($table = null)
  {
    if($table === null)
      $table = $this->_node_table;

    $flipped = array_flip($this->_column_map);

    foreach($this->_db_table->getColumnsForSelect() as $name)
    {
      if(isset($flipped[$name]))
        $alias = $flipped[$name];
      else
        $alias = $name;

      $sql_exec_fields[] = "{$table}.{$name} AS {$alias}";
    }

    return implode(', ', $sql_exec_fields);
  }

  function _processUserValues($values)
  {
    $processed = array();
    foreach($values as $name => $value)
    {
      if(isset($this->_column_map[$name]))
        $column = $this->_column_map[$name];
      else
        $column = $name;

      if(in_array($column, $this->_system_columns))
        continue;

      $processed[$column] = $value;
    }
    return $processed;
  }

  function initTree()
  {
    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}");
    $stmt->execute();

    return $this->_createRootNode();
  }

  function getRootNode()
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE {$this->_level}=0";
    $stmt = $this->_conn->newStatement($sql);

    if($root_node = $stmt->getOneRecord())
      return $root_node;

    return null;
  }

  function getParents($node)
  {
    $child = $this->_ensureNode($node);

    if($child['level'] < 1)
      return null;

   $sql =  "SELECT " . $this->_getSelectFields() . "
            FROM  {$this->_node_table}
            WHERE {$this->_left} < {$child['c_left']}
            AND {$this->_right} >  {$child['c_right']}
            ORDER BY {$this->_left} ASC";

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getRecordSet();
  }

  function getParent($node)
  {
    $child = $this->_ensureNode($node);

    if($child['level'] < 1)
      return null;
     $sql =  "SELECT " . $this->_getSelectFields() . "
              FROM  {$this->_node_table}
              WHERE {$this->_left} < {$child['c_left']}
              AND {$this->_right} >  {$child['c_right']}
              AND {$this->_level} = ".($child['level']-1);

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getOneRecord();
  }

  function getSiblings($node)
  {
    $sibling = $this->_ensureNode($node);

    if(!$parent = $this->getParent($sibling['id']))
      return new lmbCollection(array($sibling));

    return $this->getChildren($parent['id']);
  }

  function getChildren($node, $depth = 1)
  {
    return $this->_getChildren($node, $depth);
  }

  function getChildrenAll($node)
  {
    return $this->_getChildren($node);
  }

  protected function _getChildren($node, $depth = -1)
  {
    $parent = $this->_ensureNode($node);

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE {$parent['c_left']} < {$this->_left}
            AND {$parent['c_right']} > {$this->_right}";
    if($depth!=-1)
      $sql .= " AND {$this->_level} <= ".($parent['level']+$depth);
    $sql .= " ORDER BY {$this->_left}";

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getRecordSet();
  }

  function countChildren($node, $depth = 1)
  {
    return $this->_countChildren($node, $depth);
  }

  function countChildrenAll($node)
  {
    return $this->_countChildren($node);
  }

  protected function _countChildren($node, $depth = -1)
  {
    $parent = $this->_ensureNode($node);

    $sql = "SELECT count({$this->_id}) as counter
            FROM {$this->_node_table}
            WHERE {$parent['c_left']} < {$this->_left}
            AND {$parent['c_right']} > {$this->_right}";

    if($depth!=-1)
      $sql .= " AND {$this->_level} <= ".($parent['level']+$depth);

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getOneValue();
  }

  function getNode($node)
  {
    if(is_string($node) && !is_numeric($node))
    {
      if(!$res = $this->getNodeByPath($node))
        return null;
      return $res;
    }

    if(is_array($node) or is_object($node))
    {
      if(isset($node['id']))
        $id = $node['id'];
      else
        return null;
    }

    if(!isset($id))
      $id = $node;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE {$this->_id}=:id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger('id', $id);

    if($r = $stmt->getOneRecord())
      return $r;

    return null;
  }

  function getNodeByPath($path)
  {
    $path = preg_replace('~\/+~', '/', $path);

    if($path == '/')
      return $this->getRootNode();

    $path_array = explode('/', $path);

    //if(reset($path_array) == '')
      array_shift($path_array);
    if(end($path_array) == '')
      array_pop($path_array);

    if(!$path_array)
      return null;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE {$this->_level}=0";
    $stmt = $this->_conn->newStatement($sql);

    if(!$root_node = $stmt->getOneRecord())
      return null;

    $t='';
    $w="t0.{$this->_id}=".$root_node['id'];
    for($i=0;$i<count($path_array);$i++)
    {
      $c=$i+1;
      $t.=",\n{$this->_node_table} t".$c;
      $w.=" AND t".$c.".{$this->_identifier}='".$this->_conn->escape($path_array[$i])."'
            AND t".$c.".{$this->_left} BETWEEN t".$i.".{$this->_left}+1 AND t".$i.".{$this->_right}
            AND t".$c.".{$this->_level}=t".$i.".{$this->_level}+1";
    }
    $sql = "SELECT ".$this->_getSelectFields('t'.$c)." FROM {$this->_node_table} t0$t WHERE $w";

    $stmt = $this->_conn->newStatement($sql);
    if($r = $stmt->getOneRecord())
      return $r;

    return null;
  }

  function getPathToNode($node, $delimeter = '/')
  {
    $node = $this->_ensureNode($node);

    $path = '';

    if(!$parents = $this->getParents($node))
      return $path .= $delimeter . $node['identifier'];

    foreach($parents as $parent)
      $path .= $delimeter . $parent['identifier'];

    $path .= $delimeter . $node['identifier'];
    return substr($path, 1);
  }

  function getNodesByIds($ids)
  {
    if(!$ids)
      return new lmbCollection();

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE " . $this->_dbIn($this->_id, $ids) . "
            ORDER BY {$this->_left}";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function isNode($id)
  {
    return ($this->getNode($id) !== null);
  }

  function _dbIn($column_name, $values)
  {
    return $this->_conn->getExtension()->in($column_name, $values);
  }

  function createNode($node, $user_values)
  {
    $parent_node = $this->_ensureNode($node);

    $values = $this->_processUserValues($user_values);

    if(!isset($values[$this->_identifier]) || $values[$this->_identifier] == '')
      throw new lmbTreeConsistencyException("Identifier property is required");

    $this->_ensureUniqueSiblingIdentifier($values[$this->_identifier], $parent_node);

    // creating a place for the record being inserted
    $sql = "UPDATE {$this->_node_table}
            SET {$this->_left}= CASE WHEN {$this->_left}>{$parent_node['c_right']} THEN {$this->_left}+2 ELSE {$this->_left} END,
                {$this->_right}=CASE WHEN {$this->_right}>={$parent_node['c_right']} THEN {$this->_right}+2 ELSE {$this->_right} END
            WHERE {$this->_right}>={$parent_node['c_right']}";
    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    $values[$this->_parent_id] = $parent_node['id'];
    $values[$this->_left] = $parent_node['c_right'];
    $values[$this->_right] = $parent_node['c_right']+1;
    $values[$this->_level] = $parent_node['level']+1;

    $id = $this->_db_table->insert($values);
    return $id;
  }

  protected function _createRootNode()
  {
    $values = array();
    $values[$this->_parent_id] = 0;
    $values[$this->_left] = 1;
    $values[$this->_right] = 2;
    $values[$this->_level] = 0;
    $values[$this->_identifier] = '';

    $id = $this->_db_table->insert($values);

    return $id;
  }

  protected function _ensureNode($node)
  {
    if(!$res = $this->getNode($node))
      throw new lmbTreeInvalidNodeException($node);
    return $res;
  }

  protected function _ensureUniqueSiblingIdentifier($identifier, $parent_node)
  {
    $sql = "SELECT {$this->_identifier} FROM {$this->_node_table}
            WHERE
            {$this->_identifier}=:identifier:
            AND {$this->_left} > {$parent_node['c_left']}
            AND {$this->_right} < {$parent_node['c_right']}
            AND {$this->_level} = ".($parent_node['level']+1);

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setVarChar('identifier', $identifier);

    if($stmt->getOneRecord())
      throw new lmbTreeConsistencyException("There's already a sibling with such an identifier '$identifier'");
  }

  function updateNode($node, $user_values, $internal = false)
  {
    $node = $this->_ensureNode($node);

    if(isset($user_values['identifier']))
    {
      if($node['c_left'] == 1 && $user_values['identifier'])
        throw new lmbTreeConsistencyException('Root node is forbidden to have an identifier');

      if($node['identifier'] != $user_values['identifier'])
        $this->_ensureUniqueSiblingIdentifier($user_values['identifier'], $this->getParent($node));
    }

    if(!$internal)
      $values = $this->_processUserValues($user_values);
    else
      $values = $user_values;

    if(!$values)
      return false;

    $this->_db_table->updateById($node['id'], $values);

    return true;
  }

  function deleteNode($node)
  {
    $node = $this->_ensureNode($node);

    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}
                                        WHERE
                                        {$this->_left} BETWEEN  {$node['c_left']} AND {$node['c_right']}");
    $stmt->execute();

    $delta = ($node['c_right'] - $node['c_left'])+1;
    $sql = "UPDATE {$this->_node_table}
            SET {$this->_left}=CASE WHEN {$this->_left}>{$node['c_left']} THEN {$this->_left}-{$delta} ELSE {$this->_left} END,
                {$this->_right}=CASE WHEN {$this->_right}>{$node['c_left']} THEN {$this->_right}-{$delta} ELSE {$this->_right} END
            WHERE {$this->_right}>{$node['c_right']}";
    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    return true;
  }

  function deleteAll()
  {
    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}");
    $stmt->execute();
  }

  function moveNode($source, $target)
  {
    $source_node = $this->_ensureNode($source);
    $target_node = $this->_ensureNode($target);

    if($source_node['id'] == $target_node['id'])
      throw new lmbTreeConsistencyException("Can not move node into itself('{$source_node['id']}')");

    if($source_node == $this->getRootNode())
      throw new lmbTreeConsistencyException("Can not move root node");

    if($target_node == $this->getParent($source_node))
      throw new lmbTreeConsistencyException("Can not move parent node('{$source_node['id']}') into child node('{$target_node['id']}')");

    $sql = "SELECT 1 FROM {$this->_node_table} WHERE {$this->_id} = {$target_node['id']} AND {$this->_left} > {$source_node['c_left']} AND {$this->_right} < {$source_node['c_right']}";
    $stmt = $this->_conn->newStatement($sql);
    if($stmt->getOneValue())
      throw new lmbTreeConsistencyException("Can not move parent node('{$source_node['id']}') into child node('{$target_node['id']}')");

    $parent_id = "{$this->_parent_id} = CASE WHEN {$this->_id} = {$source_node['id']} THEN {$target_node['id']} ELSE {$this->_parent_id} END";

    // whether it is being moved upwards along the path
    if($target_node['c_left'] < $source_node['c_left'] && $target_node['c_right'] > $source_node['c_right'] && $target_node['level'] < ($source_node['level'] - 1) )
    {
      $sql = "UPDATE {$this->_node_table} SET
              {$this->_level}=CASE WHEN {$this->_left} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN ".$this->_level.sprintf('%+d', -($source_node['level']-1)+$target_node['level'])." ELSE {$this->_level} END,
              {$this->_right}=CASE WHEN {$this->_right} BETWEEN ".($source_node['c_right']+1)." AND ".($target_node['c_right']-1)." THEN {$this->_right}-".($source_node['c_right']-$source_node['c_left']+1)." ELSE
               CASE WHEN {$this->_left} BETWEEN {$source_node['c_left']} AND ({$source_node['c_right']}) THEN {$this->_right}+".((($target_node['c_right']-$source_node['c_right']-$source_node['level']+$target_node['level'])/2)*2 + $source_node['level'] - $target_node['level'] - 1)." ELSE {$this->_right} END END,
              {$this->_left}= CASE WHEN {$this->_left} BETWEEN ".($source_node['c_right']+1)." AND ".($target_node['c_right']-1)." THEN {$this->_left}-".($source_node['c_right']-$source_node['c_left']+1)." ELSE
               CASE WHEN {$this->_left} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN {$this->_left}+".((($target_node['c_right']-$source_node['c_right']-$source_node['level']+$target_node['level'])/2)*2 + $source_node['level'] - $target_node['level'] - 1)." ELSE {$this->_left} END END
              ,{$parent_id}
              WHERE {$this->_left} BETWEEN ".($target_node['c_left']+1)." AND ".($target_node['c_right']-1);
    }
    elseif($target_node['c_left'] < $source_node['c_left'])
    {
      $sql = "UPDATE {$this->_node_table} SET
              {$this->_level}= CASE WHEN {$this->_left} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN ".$this->_level.sprintf('%+d', -($source_node['level']-1)+$target_node['level']).' ELSE '.$this->_level." END,
              {$this->_left}= CASE WHEN {$this->_left} BETWEEN {$target_node['c_right']} AND ".($source_node['c_left']-1)." THEN {$this->_left}+".($source_node['c_right']-$source_node['c_left']+1)." ELSE
              CASE WHEN {$this->_left} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN {$this->_left}-".($source_node['c_left']-$target_node['c_right'])." ELSE {$this->_left} END END,
              {$this->_right}= CASE WHEN {$this->_right} BETWEEN {$target_node['c_right']} AND {$source_node['c_left']} THEN {$this->_right}+".($source_node['c_right']-$source_node['c_left']+1)." ELSE
              CASE WHEN {$this->_right} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN {$this->_right}-".($source_node['c_left']-$target_node['c_right'])." ELSE {$this->_right} END END
              ,{$parent_id}
              WHERE {$this->_left} BETWEEN {$target_node['c_left']} AND {$source_node['c_right']}
              OR {$this->_right} BETWEEN {$target_node['c_left']} AND {$source_node['c_right']}";

    }
    else
    {
       $sql = "UPDATE {$this->_node_table} SET
              {$this->_level}=CASE WHEN {$this->_left} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN ".$this->_level.sprintf('%+d', -($source_node['level']-1)+$target_node['level'])." ELSE {$this->_level} END,
              {$this->_left}=CASE WHEN {$this->_left} BETWEEN {$source_node['c_right']} AND {$target_node['c_right']} THEN {$this->_left}-".($source_node['c_right']-$source_node['c_left']+1)." ELSE
              CASE WHEN {$this->_left} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN {$this->_left}+".($target_node['c_right']-1-$source_node['c_right'])." ELSE {$this->_left} END END,
              {$this->_right}=CASE WHEN {$this->_right} BETWEEN ".($source_node['c_right']+1)." AND ".($target_node['c_right']-1)." THEN {$this->_right}-".($source_node['c_right']-$source_node['c_left']+1)." ELSE
              CASE WHEN {$this->_right} BETWEEN {$source_node['c_left']} AND {$source_node['c_right']} THEN {$this->_right}+".($target_node['c_right']-1-$source_node['c_right'])." ELSE {$this->_right} END END
              ,{$parent_id}
              WHERE {$this->_left} BETWEEN {$source_node['c_left']} AND {$target_node['c_right']}
              OR {$this->_right} BETWEEN {$source_node['c_left']} AND {$target_node['c_right']}";
    }

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    return true;
  }

  function moveNodeUp($node)
  {
    return $this->_moveNodeByStep($node);
  }

  function moveNodeDown($node)
  {
    return $this->_moveNodeByStep($node, 'down');
  }

  protected function _moveNodeByStep ($node , $step = 'up')
  {
    if(!$node = $this->getNode($node))
      return false;

    if(strtolower($step) == 'up')
      $conditions = $this->_right.'='.($node['c_left']-1);
    else
      $conditions = $this->_left.'='.($node['c_right']+1);

    $sql = "SELECT {$this->_left} AS c_left, {$this->_right} AS c_right
            FROM {$this->_node_table}
            WHERE {$conditions} AND {$this->_level}={$node['level']}";
    $stmt =  $this->_conn->newStatement($sql);
    if(!$node2 = $stmt->getOneRecord())
      return false;

    $delta = ($step == 'up') ? ($node['c_left'] - $node2['c_left']) : ($node2['c_left'] - $node['c_left']);
    $delta2 = ($step == 'up') ? ($node['c_right'] - $node2['c_right']) : ($node2['c_right'] - $node['c_right']);

    $sql = "UPDATE {$this->_node_table} SET
            {$this->_right} = CASE
                  WHEN {$this->_left} BETWEEN {$node['c_left']} AND {$node['c_right']}
                    THEN ".( ($step == 'up') ? $this->_right.'-'.$delta : $this->_right.'+'.$delta2)."
                    ELSE ".( ($step == 'up') ? $this->_right.'+'.$delta2 : $this->_right.'-'.$delta)."
                  END,
            {$this->_left} = CASE
                  WHEN {$this->_left} BETWEEN {$node['c_left']} AND {$node['c_right']}
                    THEN ".( ($step == 'up') ? $this->_left.'-'.$delta : $this->_left.'+'.$delta2)."
                    ELSE ".( ($step == 'up') ? $this->_left.'+'.$delta2 : $this->_left.'-'.$delta)."
                  END
            WHERE ".(($step == 'up') ? $this->_left.'>='.$node2['c_left'].' AND '.$this->_right.'<='.$node['c_right'] : $this->_left.'>='.$node['c_left'].' AND '.$this->_right.'<='.$node2['c_right']);

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    return true;
  }
}


