<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMaterializedPathTree.class.php 5680 2007-04-19 07:18:38Z pachanga $
 * @package    tree
 */
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/tree/src/lmbTree.interface.php');
lmb_require('limb/tree/src/exception/lmbTreeException.class.php');
lmb_require('limb/tree/src/exception/lmbTreeInvalidNodeException.class.php');
lmb_require('limb/tree/src/exception/lmbTreeConsistencyException.class.php');

class lmbMPTree implements lmbTree
{
  protected $_conn = null;

  protected $_system_columns = array();
  protected $_select_columns = '';

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

    foreach($this->_db_table->getColumnsForSelect() as $full_name => $name)
      $sql_exec_fields[] = $full_name . ' AS ' . $name;

    $this->_select_columns = implode(', ', $sql_exec_fields);
    return $this->_select_columns;
  }

  function _processUserValues($values)
  {
    $processed = array();
    foreach($values as $field => $value)
    {
      if(in_array($field, $this->_system_columns))
        continue;

      $processed[$field] = $value;
    }
    return $processed;
  }

  function getParents($node)
  {
    $child = $this->_ensureNode($node);

    if($child[$this->_level] < 1)
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
    $stmt->setVarChar('level', $child[$this->_level]);
    $stmt->setVarChar('id', $child[$this->_id]);

    return $stmt->getRecordSet();
  }

  function getParent($node)
  {
    $child = $this->_ensureNode($node);
    return $this->getNode($child[$this->_parent_id]);
  }

  function getSiblings($node)
  {
    $me = $this->_ensureNode($node);

    if(!$me[$this->_parent_id])
      return new lmbCollection(array($me));

    return $this->getChildren($me[$this->_parent_id]);
  }

  function getChildren($node, $depth = 1)
  {
    $parent = $this->_ensureNode($node);

    if($depth == 1)
    {
      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE {$this->_parent_id} = :parent_id:
              ORDER BY {$this->_path}";

      $stmt = $this->_conn->newStatement($sql);
      $stmt->set($this->_parent_id, $parent['id']);
    }
    else
    {
      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE {$this->_path} LIKE '{$parent['path']}%'
              AND {$this->_id} != {$parent['id']}";
      if($depth != -1)
        $sql .= " AND {$this->_level} < ". ($parent[$this->_level] + 1 + $depth);
      $sql .= " ORDER BY {$this->_path}";
      $stmt = $this->_conn->newStatement($sql);
    }

    return $stmt->getRecordSet();
  }

  function getChildrenAll($node)
  {
    $node = $this->_ensureNode($node);

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE {$this->_path} LIKE '{$node[$this->_path]}%'
            AND {$this->_id} != {$node[$this->_id]}
            ORDER BY {$this->_path}";

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getRecordSet();
  }

  function countChildren($node, $depth = 1)
  {
    $parent = $this->_ensureNode($node);

    if($depth == 1)
    {
      $sql = "SELECT count({$this->_id}) as counter FROM {$this->_node_table}
              WHERE {$this->_parent_id} = :parent_id:";

      $stmt = $this->_conn->newStatement($sql);
      $stmt->set($this->_parent_id, $parent['id']);
    }
    else
    {
      $sql = "SELECT count({$this->_id}) as counter
              FROM {$this->_node_table}
              WHERE {$this->_path} LIKE '{$parent[$this->_path]}%'
              AND {$this->_id} != {$parent[$this->_id]}";
      if($depth !=-1)
        $sql .= " AND {$this->_level} < " . ($parent[$this->_level] + 1 + $depth);
      $stmt = $this->_conn->newStatement($sql);
    }
    return $stmt->getOneValue();
  }

  function countChildrenAll($node)
  {
    $parent = $this->_ensureNode($node);

    $sql = "SELECT count({$this->_id}) as counter
            FROM {$this->_node_table}
            WHERE {$this->_path} LIKE '{$parent[$this->_path]}%'
            AND {$this->_id} != {$parent[$this->_id]}";

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
    elseif(is_array($node))
    {
      if(isset($node[$this->_id]))
        $id = $node[$this->_id];
      else
        return null;
    }
    elseif(is_object($node))
    {
      if(!$id = $node->get($this->_id))
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

    $level = sizeof($path_array);

    $in_condition = $this->_dbIn($this->_identifier, array_unique($path_array));

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE
            {$in_condition}
            AND {$this->_level} <= {$level}
            ORDER BY {$this->_path}";

    $stmt = $this->_conn->newStatement($sql);
    $rs = $stmt->getRecordSet();

    $curr_level = 0;
    $parent_id = null;
    $path_to_node = '';

    foreach($rs as $node)
    {
      if($node[$this->_level] < $curr_level)
        continue;

      if($node[$this->_identifier] == $path_array[$curr_level] &&
         (!$parent_id ||
         $node[$this->_parent_id] == $parent_id))
      {
        $parent_id = $node[$this->_id];

        $curr_level++;
        $path_to_node .= '/' . $node[$this->_identifier];

        if($curr_level == $level)
          return $node;
      }
    }
    return null;
  }

  function getPathToNode($node)
  {
    $node = $this->_ensureNode($node);

    if(!$parents = $this->getParents($node[$this->_id]))
      return '/';

    $path = '';
    foreach($parents as $parent)
      $path .= $parent[$this->_identifier] . '/';

    return $path .= $node[$this->_identifier];
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

  function getMaxChildIdentifier($node)
  {
    $parent = $this->_ensureNode($node);

    $sql = "SELECT identifier FROM {$this->_node_table}
            WHERE
            {$this->_parent_id}=:parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger('parent_id', $parent[$this->_id]);

    if($arr = $stmt->getOneColumnAsArray())
    {
      uasort($arr, 'strnatcmp');
      return end($arr);
    }
    else
      return 0;
  }

  function isNode($node)
  {
    return ($this->getNode($node) !== null);
  }

  function updateNode($node, $values, $internal = false)
  {
    $node = $this->_ensureNode($node);

    if(isset($values[$this->_identifier]))
    {
      if($node[$this->_parent_id] == 0 && $values[$this->_identifier])//root check
        throw new lmbTreeConsistencyException('Root node is forbidden to have an identifier');

      if($node[$this->_identifier] != $values[$this->_identifier])
        $this->_ensureUniqueSiblingIdentifier($values[$this->_identifier], $node[$this->_parent_id]);
    }

    if($internal === false)
      $values = $this->_processUserValues($values);

    if(!$values)
      return;

    $this->_db_table->updateById($node[$this->_id], $values);
  }

  function _getNextNodeInsertId()
  {
    //if field is autoincremented why do we need it?
    $sql = "SELECT MAX({$this->_id}) as m FROM {$this->_node_table}";
    $stmt = $this->_conn->newStatement($sql);
    $max = $stmt->getOneValue();
    return isset($max) ? $max + 1 : 1;
  }

  function _dbConcat($values)
  {
    switch($this->_conn->getType())
    {
      case 'mysql':
        $str = implode(',', $values);
        return " CONCAT({$str}) ";

      case 'pgsql':
        return '(' . implode(' || ', $values) . ')';

      default:
        throw new lmbException("This database type '" . $this->_conn->getType() . "' is not supported(concat operation)");
    }
  }

  function _dbSubstr($string, $offset, $limit=null)
  {
    switch($this->_conn->getType())
    {
      case 'mysql':
      case 'pgsql':
        if($limit === null)
          return " SUBSTRING({$string} FROM {$offset}) ";
        else
          return " SUBSTRING({$string} FROM {$offset} FOR {$limit}) ";

      default:
        throw new lmbException("This database type '" . $this->_conn->getType() . "' is not supported(substr operation)");
    }
  }

  function _dbIn($column_name, $values)
  {
    $in_ids = implode("','", $values);
    return $column_name . " IN ('" . $in_ids . "')";
  }

  protected function _createRootNode()
  {
    $new_values[$this->_id] = $this->_getNextNodeInsertId();
    $new_values[$this->_path] = '/' . $new_values[$this->_id] . '/';
    $new_values[$this->_level] = 0;
    $new_values[$this->_parent_id] = 0;

    $this->_db_table->insert($new_values);

    return $new_values[$this->_id];
  }

  protected function _ensureNode($node)
  {
    if(!$res = $this->getNode($node))
      throw new lmbTreeInvalidNodeException($node);
    return $res;
  }

  protected function _ensureUniqueSiblingIdentifier($identifier, $parent_id)
  {
    $sql = "SELECT identifier FROM {$this->_node_table}
            WHERE
            {$this->_identifier}=:identifier: AND
            {$this->_parent_id}=:parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setVarChar('identifier', $identifier);
    $stmt->setInteger('parent_id', $parent_id);
    if($stmt->getOneRecord())
      throw new lmbTreeConsistencyException("There's already a sibling with such an identifier '$identifier'");
  }

  function createNode($node, $values)
  {
    $parent_node = $this->_ensureNode($node);

    $parent_id = $parent_node[$this->_id];

    $new_values = $this->_processUserValues($values);

    if(!isset($new_values[$this->_identifier]) || $new_values[$this->_identifier] == '')
      throw new lmbTreeConsistencyException("Identifier property is required");

    $this->_ensureUniqueSiblingIdentifier($new_values[$this->_identifier], $parent_id);

    $new_values[$this->_id] = $this->_getNextNodeInsertId();
    $new_values[$this->_level] = $parent_node[$this->_level] + 1;
    $new_values[$this->_parent_id] = $parent_id;
    $new_values[$this->_path] = $parent_node[$this->_path] . $new_values[$this->_id] . '/';

    $this->_db_table->insert($new_values);

    return $new_values[$this->_id];
  }

  function deleteNode($node)
  {
    $node = $this->_ensureNode($node);

    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}
                                        WHERE
                                        {$this->_path} LIKE :path:");

    $stmt->setVarChar('path', $node[$this->_path] . '%');
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

    if($source_node[$this->_path] == $target_node[$this->_path])
      throw new lmbTreeConsistencyException("Can not move node into itself('{$source_node[$this->_id]}')");

    if(strstr($target_node[$this->_path], $source_node[$this->_path]) !== false)
      throw new lmbTreeConsistencyException("Can not move parent node('{$source_node[$this->_id]}') into child node('{$target_node[$this->_id]}')");

    $id = $source_node[$this->_id];
    $target_id = $target_node[$this->_id];

    $move_values = array($this->_parent_id => $target_id);

    $this->_db_table->updateById($id, $move_values);

    $src_path_len = strlen($source_node[$this->_path]);
    $sub_string = $this->_dbSubstr($this->_path, 1, $src_path_len);
    $sub_string2 = $this->_dbSubstr($this->_path, $src_path_len);

    $path_set =
      $this->_dbConcat( array(
        "'{$target_node[$this->_path]}'" ,
        "'{$id}'",
        $sub_string2)
      );

    $sql = "UPDATE {$this->_node_table}
            SET
            {$this->_path} = {$path_set},
            {$this->_level} = {$this->_level} + {$target_node[$this->_level]} - {$source_node[$this->_level]} + 1
            WHERE
            {$sub_string} = '{$source_node[$this->_path]}' OR
            {$this->_path} = '{$source_node[$this->_path]}'";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();
  }
}

?>
