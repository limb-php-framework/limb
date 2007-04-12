<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMaterializedPathTree.class.php 5645 2007-04-12 07:13:10Z pachanga $
 * @package    tree
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/tree/src/tree/lmbTree.interface.php');

class lmbMaterializedPathTree implements lmbTree
{
  protected $_conn = null;

  protected $_params = array();

  protected $_required_params = array('id', 'root_id', 'path', 'level', 'children');

  protected $_dumb_mode = false;

  protected $_db_table = false;

  function __construct($node_table = 'sys_tree')
  {
    $toolkit = lmbToolkit :: instance();

    $this->_conn = $toolkit->getDefaultDbConnection();
    $this->_node_table = $node_table;
    $this->_db_table = new lmbTableGateway($this->_node_table);
    $this->_params = $this->_db_table->getColumnNames();
  }

  function setDumbMode($status=true)
  {
    $prev_mode = $this->_dumb_mode;
    $this->_dumb_mode = $status;
    return $prev_mode;
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
    $sql_exec_fields = array();
    foreach ($this->_params as $name)
    {
      $sql_exec_fields[] = $this->_node_table . '.' . $name . ' AS ' . $name;
    }

    return implode(', ', $sql_exec_fields);
  }

  function _processUserValues($values)
  {
    if ($this->_dumb_mode)
      return $values;

    $processed = array();
    foreach($values as $field => $value)
    {
      if (!in_array($field, $this->_params))
        continue;

      if (in_array($field, $this->_required_params))
        continue;

      $processed[$field] = $value;
    }
    return $processed;
  }

  function getRootNodes()
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE parent_id=0";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function getParents($node)
  {
    if(!$child = $this->getNode($node))
      return null;

    $join_table = $this->_node_table . '2';
    $concat = $this->_dbConcat(array($this->_node_table . '.path', "'%'"));

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}, {$this->_node_table} AS  {$join_table}
            WHERE
            {$join_table}.path LIKE {$concat} AND
            {$this->_node_table}.root_id = :root_id: AND
            {$this->_node_table}.level < :level: AND
            {$join_table}.id = :id:
            ORDER BY {$this->_node_table}.level ASC";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger('root_id', $child['root_id']);
    $stmt->setVarChar('level', $child['level']);
    $stmt->setVarChar('id', $child['id']);

    return $stmt->getRecordSet();
  }

  function getParent($node)
  {
    if (!$child = $this->getNode($node))
      return null;

    if ($child['id'] == $child['root_id'])
      return null;

    return $this->getNode($child['parent_id']);
  }

  function getSiblings($node)
  {
    if (!($sibling = $this->getNode($node)))
      return null;

    $parent = $this->getParent($sibling['id']);
    return $this->getChildren($parent['id']);
  }

  function getChildren($node)
  {
    if (!$parent = $this->getNode($node))
      return null;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE parent_id = :parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->set('parent_id', $parent['id']);

    return $stmt->getRecordSet();
  }

  function countChildren($node)
  {
    if (!$parent = $this->getNode($node))
      return null;

    $sql = "SELECT count(id) as counter FROM {$this->_node_table}
            WHERE parent_id = :parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->set('parent_id', $parent['id']);
    return $stmt->getOneValue();
  }

  function getSubBranch($node, $depth = -1, $include_parent = false)
  {
    if (!$parent_node = $this->getNode($node))
      return null;

    $id = $parent_node['id'];

    if ($depth != -1)
      $depth_condition = " AND level <=" . ($parent_node['level'] + $depth);
    else
      $depth_condition = '';

    if($include_parent)
      $include_parent_condition = '';
    else
      $include_parent_condition = " AND id!={$id}";

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE
            path LIKE '{$parent_node['path']}%%'
            {$depth_condition}
            {$include_parent_condition}
            ORDER BY path";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function getSubBranchByPath($path, $depth = -1, $include_parent = false)
  {
    if(!$parent_node = $this->getNodeByPath($path))
      return null;

    return $this->getSubBranch($parent_node, $depth, $include_parent);
  }

  function getNode($node)
  {
    if(is_array($node))
      return $node;
    else
      $id = $node;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE id=:id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger('id', $id);

    if($r = $stmt->getOneRecord())
      return $r;

    return null;
  }

  function getNodeByPath($path, $delimiter='/')
  {
    $path_array = explode($delimiter, $path);

    array_shift($path_array);

    if(end($path_array) == '')
      array_pop($path_array);

    $level = sizeof($path_array);

    if(!count($path_array))
      return null;

    $in_condition = $this->_dbIn('identifier', array_unique($path_array));

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE
            {$in_condition}
            AND level <= {$level}
            ORDER BY path";

    $stmt = $this->_conn->newStatement($sql);
    $rs = $stmt->getRecordSet();

    $curr_level = 0;
    $parent_id = 0;
    $path_to_node = '';

    for($rs->rewind();$rs->valid();$rs->next())
    {
      $record = $rs->current();
      $node = $record;

      if ($node['level'] < $curr_level)
        continue;

      if($node['identifier'] == $path_array[$curr_level] &&
         $node['parent_id'] == $parent_id)
      {
        $parent_id = $node['id'];

        $curr_level++;
        $path_to_node .= $delimiter . $node['identifier'];

        if ($curr_level == $level)
          return $node;
      }
    }

    return null;
  }

  function getPathToNode($node, $delimeter = '/')
  {
    if(!$node = $this->getNode($node))
      return null;

    $parents = $this->getParents($node['id']);

    $path = '';
    for($parents->rewind();$parents->valid();$parents->next())
    {
      $r = $parents->current();
      $path .= $delimeter . $r->get('identifier');
    }

    return $path .= $delimeter . $node['identifier'];
  }

  function getNodesByIds($ids)
  {
    if(!$ids)
      return new lmbCollection();

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE " . $this->_dbIn('id', $ids) . "
            ORDER BY path";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function getMaxChildIdentifier($node)
  {
    if (!($parent = $this->getNode($node)))
      return false;

    $sql = "SELECT identifier FROM {$this->_node_table}
            WHERE
            root_id=:root_id: AND
            parent_id=:parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger('root_id', $parent['root_id']);
    $stmt->setInteger('parent_id', $parent['id']);

    if($arr = $stmt->getOneColumnAsArray())
    {
      uasort($arr, 'strnatcmp');
      return end($arr);
    }
    else
      return 0;
  }

  function isNode($id)
  {
    return ($this->getNode($id) !== null);
  }

  function updateNode($id, $values, $internal = false)
  {
    if(!$this->isNode($id))
      return false;

    if($internal === false)
      $values = $this->_processUserValues($values);

    if(!$values)
      return false;

    $node = $this->getNode($id);

    if (isset($values['parent_id']) && $node['parent_id'] != $values['parent_id'])
      $this->moveTree($id, $values['parent_id']);

    $this->_db_table->updateById($id, $values);

    return true;//???
  }

  function _getNextNodeInsertId()
  {
    $sql = 'SELECT MAX(id) as m FROM '. $this->_node_table;
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
        throw new Exception("This database type '" . $this->_conn->getType() . "' is not supported(concat operation)");
    }
  }

  //the same story...
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
        throw new Exception("This database type '" . $this->_conn->getType() . "' is not supported(substr operation)");
    }
  }

  function _dbIn($column_name, $values)
  {
    $in_ids = implode("','", $values);
    return $column_name . " IN ('" . $in_ids . "')";
  }

  function createRootNode($values)
  {
    $new_values = $this->_processUserValues($values);

    if(!isset($values['id']))
    {
      $new_values['id'] = $this->_getNextNodeInsertId();
    }
    else
    {
      $new_values['id'] = (int)$values['id'];

      if($this->isNode($new_values['id']))//must be fatal!!!
        return false;
    }

    $new_values['root_id'] = $new_values['id'];
    $new_values['path'] = '/' . $new_values['id'] . '/';
    $new_values['level'] = 1;
    $new_values['parent_id'] = 0;
    $new_values['children'] = 0;

    $this->_db_table->insert($new_values);

    return $new_values['id'];
  }

  function createNode($values, $parent_node = null)
  {
    if(!$parent_node)
      return $this->createRootNode($values);
    else
      return $this->createSubNode($parent_node, $values);
  }

  function createSubNode($node, $values)
  {
    if (!$parent_node = $this->getNode($node))
      return false;

    $parent_id = $parent_node['id'];

    $new_values = $this->_processUserValues($values);

    if (!isset($values['id']))
    {
      $new_values['id'] = $this->_getNextNodeInsertId();
    }
    else
    {
      $new_values['id'] = (int)$values['id'];

      if($this->isNode($new_values['id']))//must be fatal!!!
        return false;
    }

    $new_values['root_id'] = $parent_node['root_id'];
    $new_values['level'] = $parent_node['level'] + 1;
    $new_values['parent_id'] = $parent_id;
    $new_values['path'] = $parent_node['path'] . $new_values['id'] . '/';
    $new_values['children'] = 0;

    $this->_db_table->insert($new_values);

    $this->_db_table->updateById($parent_id, array('children' => $parent_node['children'] + 1));

    return $new_values['id'];
  }

  function deleteNode($node)
  {
    if (!$node = $this->getNode($node))
      return false;

    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}
                                        WHERE
                                        path LIKE :path: AND
                                        root_id = :root_id:");

    $stmt->setVarChar('path', $node['path'] . '%');
    $stmt->setInteger('root_id', $node['root_id']);

    $stmt->execute();

    $stmt = $this->_conn->newStatement("UPDATE {$this->_node_table}
                                        SET children = children - 1
                                        WHERE
                                        id = :id:");

    $stmt->setInteger('id', $node['parent_id']);

    $stmt->execute();

    return true;
  }

  function moveTree($source_node, $target_node)
  {
    if ($source_node == $target_node)
      return false;

    if (!$source_node = $this->getNode($source_node))
      return false;

    if (!$target_node = $this->getNode($target_node))
      return false;

    if (strstr($target_node['path'], $source_node['path']) !== false)
      return false;

    $id = $source_node['id'];
    $target_id = $target_node['id'];

    $move_values = array('parent_id' => $target_id);

    $this->_db_table->updateById($id, $move_values);

    $src_path_len = strlen($source_node['path']);
    $sub_string = $this->_dbSubstr('path', 1, $src_path_len);
    $sub_string2 = $this->_dbSubstr('path', $src_path_len);

    $path_set =
      $this->_dbConcat( array(
        "'{$target_node['path']}'" ,
        "'{$id}'",
        $sub_string2)
      );

    $sql = "UPDATE {$this->_node_table}
            SET
            path = {$path_set},
            level = level + {$target_node['level']} - {$source_node['level']} + 1,
            root_id = {$target_node['root_id']}
            WHERE
            {$sub_string} = '{$source_node['path']}' OR
            path = '{$source_node['path']}'";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    $sql = "UPDATE {$this->_node_table}
            SET children = children - 1
            WHERE
            id = {$source_node['parent_id']}";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    $sql = "UPDATE {$this->_node_table}
            SET children = children + 1
            WHERE
            id = {$target_id}";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    return true;
  }
}

?>
