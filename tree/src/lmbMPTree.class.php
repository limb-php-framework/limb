<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/tree/src/lmbTree.interface.php');
lmb_require('limb/tree/src/exception/lmbTreeException.class.php');
lmb_require('limb/tree/src/exception/lmbTreeInvalidNodeException.class.php');
lmb_require('limb/tree/src/exception/lmbTreeConsistencyException.class.php');

/**
 * class lmbMPTree.
 *
 * @package tree
 * @version $Id$
 */
class lmbMPTree implements lmbTree
{
  protected $_conn;

  protected $_system_columns = array();
  protected $_column_map;
  protected $_select_columns = '';

  //convenience aliases for physical table column names
  protected $_id;
  protected $_parent_id;
  protected $_identifier;
  protected $_level;
  protected $_path;

  protected $_db_table;

  function __construct($node_table = 'mp_tree',
                       $conn = null,
                       $column_map = array('id' => 'id', 'parent_id' => 'parent_id',
                                           'level' => 'level', 'identifier' => 'identifier',
                                           'path' => 'path'))
  {
    $this->_mapColumns($column_map);

    if(!$conn)
      $this->_conn = lmbToolkit :: instance()->getDefaultDbConnection();
    else
      $this->_conn = $conn;

    $this->_node_table = $node_table;
    $this->_db_table = new lmbTableGateway($this->_node_table, $this->_conn);
  }

  protected function _mapColumns($column_map)
  {
    $this->_id = isset($column_map['id']) ? $column_map['id'] : 'id';
    $this->_parent_id = isset($column_map['parent_id']) ? $column_map['parent_id'] : 'parent_id';
    $this->_level = isset($column_map['level']) ? $column_map['level'] : 'level';
    $this->_identifier = isset($column_map['identifier']) ? $column_map['identifier'] : 'identifier';
    $this->_path = isset($column_map['path']) ? $column_map['path'] : 'path';

    $this->_system_columns = array($this->_id, $this->_parent_id, $this->_level, $this->_path);

    $this->_column_map = array('id' => $this->_id, 'parent_id' => $this->_parent_id,
                               'level' => $this->_level, 'identifier' => $this->_identifier,
                               'path' => $this->_path);
  }

  function initTree()
  {
    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}");
    $stmt->execute();
    return $this->_createRootNode();
  }

  function getRootNode()
  {
    $sql = "SELECT " . $this->_getSelectFields() . " FROM {$this->_node_table} WHERE {$this->_level}=0";
    $stmt = $this->_conn->newStatement($sql);

    if($root_node = $stmt->getOneRecord())
      return $root_node;

    return null;
  }

  function setNodeTable($table_name)
  {
    $this->_node_table = $table_name;
  }

  function getNodeTable()
  {
    return $this->_node_table;
  }

  function _getSelectFields()
  {
    if($this->_select_columns)
      return $this->_select_columns;

    $flipped = array_flip($this->_column_map);

    foreach($this->_db_table->getColumnsForSelect() as $name)
    {
      if(isset($flipped[$name]))
        $alias = $flipped[$name];
      else
        $alias = $name;

      $sql_exec_fields[] = "{$this->_node_table}.{$name} AS {$alias}";
    }

    $this->_select_columns = implode(', ', $sql_exec_fields);
    return $this->_select_columns;
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

  function getParents($node)
  {
    $child = $this->_ensureNode($node);

    if($child['level'] < 1)
      return null;

    $join_table = $this->_node_table . '2';
    $concat = $this->_dbConcat(array($this->_node_table . '.' . $this->_path, "'%'"));

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}, {$this->_node_table} AS {$join_table}
            WHERE
            {$join_table}.{$this->_path} LIKE {$concat} AND
            {$this->_node_table}.{$this->_level} < :level: AND
            {$join_table}.{$this->_id} = :id:
            ORDER BY {$this->_node_table}.{$this->_level} ASC";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setVarChar('level', $child['level']);
    $stmt->setVarChar('id', $child['id']);

    return $stmt->getRecordSet();
  }

  function getParent($node)
  {
    $child = $this->_ensureNode($node);
    return $this->getNode($child['parent_id']);
  }

  function getSiblings($node)
  {
    $me = $this->_ensureNode($node);

    if(!$me['parent_id'])
      return new lmbCollection(array($me));

    return $this->getChildren($me['parent_id']);
  }

  function getChildren($node, $depth = 1)
  {
    $parent = $this->_ensureNode($node);

    if($depth == 1)
    {
      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE {$this->_parent_id} = :parent_id:";

      $stmt = $this->_conn->newStatement($sql);
      $stmt->setInteger('parent_id', $parent['id']);
    }
    else
    {
      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE {$this->_path} LIKE '{$parent['path']}%'
              AND {$this->_id} != {$parent['id']}";
      if($depth != -1)
        $sql .= " AND {$this->_level} < ". ($parent['level'] + 1 + $depth);
      $stmt = $this->_conn->newStatement($sql);
    }

    $rs = $stmt->getRecordSet();
    $rs->sort(array($this->_path => 'ASC'));
    return $rs;
  }

  function getChildrenAll($node)
  {
    $node = $this->_ensureNode($node);

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE {$this->_path} LIKE '{$node['path']}%'
            AND {$this->_id} != {$node['id']}";

    $stmt = $this->_conn->newStatement($sql);

    $rs = $stmt->getRecordSet();
    $rs->sort(array($this->_path => 'ASC'));
    return $rs;
  }

  function countChildren($node, $depth = 1)
  {
    $parent = $this->_ensureNode($node);

    if($depth == 1)
    {
      $sql = "SELECT count({$this->_id}) as counter FROM {$this->_node_table}
              WHERE {$this->_parent_id} = :parent_id:";

      $stmt = $this->_conn->newStatement($sql);
      $stmt->setInteger('parent_id', $parent['id']);
    }
    else
    {
      $sql = "SELECT count({$this->_id}) as counter
              FROM {$this->_node_table}
              WHERE {$this->_path} LIKE '{$parent['path']}%'
              AND {$this->_id} != {$parent['id']}";
      if($depth !=-1)
        $sql .= " AND {$this->_level} < " . ($parent['level'] + 1 + $depth);
      $stmt = $this->_conn->newStatement($sql);
    }
    return $stmt->getOneValue();
  }

  function countChildrenAll($node)
  {
    $parent = $this->_ensureNode($node);

    $sql = "SELECT count({$this->_id}) as counter
            FROM {$this->_node_table}
            WHERE {$this->_path} LIKE '{$parent['path']}%'
            AND {$this->_id} != {$parent['id']}";

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
    elseif(is_array($node) || is_object($node))
    {
      if(isset($node['id']))
        $id = $node['id'];
      else
        return null;
    }
    else
      $id = $node;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE {$this->_id}=:id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger($this->_id, $id);

    if($node = $stmt->getOneRecord())
      return $node;

    return null;
  }

  function getNodeByPath($path)
  {
    $path = preg_replace('~\/+~', '/', $path);

    if($path == '/')
      return $this->getRootNode();

    $path_array = explode('/', $path);

    array_shift($path_array);//skip first item
    if(end($path_array) == '')//ending slash
      array_pop($path_array);

    if(!$path_array)
      return null;

    if(!$root = $this->getRootNode())
      return null;

    $parent_id = $root['id'];
    foreach($path_array as $path)
    {
      $path = $this->_conn->escape($path);
      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE {$this->_parent_id}=$parent_id AND
                    {$this->_identifier}='$path'";
      $stmt = $this->_conn->newStatement($sql);
      if(!$node = $stmt->getOneRecord())
        return null;
      $parent_id = $node['id'];
    }
    return $node;
  }

  function getPathToNode($node)
  {
    $node = $this->_ensureNode($node);

    if(!$parents = $this->getParents($node['id']))
      return '/';

    $path = '';
    foreach($parents as $parent)
      $path .= $parent['identifier'] . '/';

    return $path .= $node['identifier'];
  }

  function getNodesByIds($ids)
  {
    if(!$ids)
      return new lmbCollection();

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE " . $this->_dbIn($this->_id, $ids) . "
            ORDER BY {$this->_path}";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function isNode($node)
  {
    return ($this->getNode($node) !== null);
  }

  function updateNode($node, $user_values, $internal = false)
  {
    $node = $this->_ensureNode($node);

    if(isset($user_values['identifier']))
    {
      if($node['parent_id'] == 0 && $user_values['identifier'])//root check
        throw new lmbTreeConsistencyException('Root node is forbidden to have an identifier');

      if($node['identifier'] != $user_values['identifier'])
        $this->_ensureUniqueSiblingIdentifier($user_values['identifier'], $node['parent_id']);
    }

    if(!$internal)
      $values = $this->_processUserValues($user_values);
    else
      $values = $user_values;

    if(!$values)
      return;

    $this->_db_table->updateById($node['id'], $values);
  }

  function _dbConcat($values)
  {
    return $this->_conn->getExtension()->concat($values);
  }

  function _dbSubstr($string, $offset, $limit=null)
  {
    return $this->_conn->getExtension()->substr($string, $offset, $limit);
  }

  function _dbIn($column_name, $values)
  {
    return $this->_conn->getExtension()->in($column_name, $values);
  }

  protected function _createRootNode()
  {
    $values = array();
    $values[$this->_level] = 0;
    $values[$this->_parent_id] = 0;
    $values[$this->_identifier] = '';

    $id = $this->_db_table->insert($values);
    $this->_db_table->updateById($id, array($this->_path => '/' . $id . '/'));

    return $id;
  }

  protected function _ensureNode($node)
  {
    if(!$res = $this->getNode($node))
      throw new lmbTreeInvalidNodeException($node);
    return $res;
  }

  protected function _ensureUniqueSiblingIdentifier($identifier, $parent_id)
  {
    $sql = "SELECT {$this->_identifier} as identifier FROM {$this->_node_table}
            WHERE
            {$this->_identifier}=:identifier: AND
            {$this->_parent_id}=:parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setVarChar('identifier', $identifier);
    $stmt->setInteger('parent_id', $parent_id);
    if($stmt->getOneRecord())
      throw new lmbTreeConsistencyException("There's already a sibling with such an identifier '$identifier'");
  }

  function createNode($parent, $user_values)
  {
    $parent_node = $this->_ensureNode($parent);

    $parent_id = $parent_node['id'];

    $values = $this->_processUserValues($user_values);

    if(!isset($values[$this->_identifier]) || $values[$this->_identifier] == '')
      throw new lmbTreeConsistencyException("Identifier property is required");

    $this->_ensureUniqueSiblingIdentifier($values[$this->_identifier], $parent_id);

    $values[$this->_level] = $parent_node['level'] + 1;
    $values[$this->_parent_id] = $parent_id;

    $id = $this->_db_table->insert($values);
    $path = $parent_node['path'] . $id . '/';
    
    $this->_db_table->updateById($id, array($this->_path => $path));

    return $id;
  }

  function deleteNode($node)
  {
    $node = $this->_ensureNode($node);

    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}
                                        WHERE
                                        {$this->_path} LIKE :path:");

    $stmt->setVarChar('path', $node['path'] . '%');
    $stmt->execute();
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

    if($source_node['path'] == $target_node['path'])
      throw new lmbTreeConsistencyException("Can not move node into itself('{$source_node['id']}')");

    if(strstr($target_node['path'], $source_node['path']) !== false)
      throw new lmbTreeConsistencyException("Can not move parent node('{$source_node['id']}') into child node('{$target_node['id']}')");

    $id = $source_node['id'];
    $target_id = $target_node['id'];

    $move_values = array($this->_parent_id => $target_id);

    $this->_db_table->updateById($id, $move_values);

    $src_path_len = strlen($source_node['path']);
    $sub_string = $this->_dbSubstr($this->_path, 1, $src_path_len);
    $sub_string2 = $this->_dbSubstr($this->_path, $src_path_len);

    $path_set =
      $this->_dbConcat( array(
        "'{$target_node['path']}'" ,
        "'{$id}'",
        $sub_string2)
      );

    $sql = "UPDATE {$this->_node_table}
            SET
            {$this->_path} = {$path_set},
            {$this->_level} = {$this->_level} + {$target_node['level']} - {$source_node['level']} + 1
            WHERE
            {$sub_string} = '{$source_node['path']}' OR
            {$this->_path} = '{$source_node['path']}'";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();
  }
}


