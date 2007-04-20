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
lmb_require('limb/tree/src/lmbTreeException.class.php');
lmb_require('limb/tree/src/lmbInvalidNodeTreeException.class.php');
lmb_require('limb/tree/src/lmbConsistencyTreeException.class.php');

class lmbMPTree implements lmbTree
{
  protected $_conn = null;
  protected $_system_fields = array('id', 'path', 'level', 'children', 'parent_id');
  protected $_select_fields = '';
  protected $_db_table = false;

  function __construct($node_table = 'sys_tree', $conn = null)
  {
    if(!$conn)
      $this->_conn = lmbToolkit :: instance()->getDefaultDbConnection();
    else
      $this->_conn = $conn;

    $this->_node_table = $node_table;
    $this->_db_table = new lmbTableGateway($this->_node_table, $this->_conn);
  }

  function initTree()
  {
    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}");
    $stmt->execute();
    return $this->_createRootNode();
  }

  function getRootNode()
  {
    $sql = "SELECT " . $this->_getSelectFields() . " FROM {$this->_node_table} WHERE level=0";
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
    if($this->_select_fields)
      return $this->_select_fields;

    foreach($this->_db_table->getColumnsForSelect() as $full_name => $name)
      $sql_exec_fields[] = $full_name . ' AS ' . $name;

    $this->_select_fields = implode(', ', $sql_exec_fields);
    return $this->_select_fields;
  }

  function _processUserValues($values)
  {
    $processed = array();
    foreach($values as $field => $value)
    {
      if(in_array($field, $this->_system_fields))
        continue;

      $processed[$field] = $value;
    }
    return $processed;
  }

  function getParents($node)
  {
    $child = $this->_ensureNode($node);

    if($child['level'] < 1)
      return null;

    $join_table = $this->_node_table . '2';
    $concat = $this->_dbConcat(array($this->_node_table . '.path', "'%'"));

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}, {$this->_node_table} AS  {$join_table}
            WHERE
            {$join_table}.path LIKE {$concat} AND
            {$this->_node_table}.level < :level: AND
            {$join_table}.id = :id:
            ORDER BY {$this->_node_table}.level ASC";

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
              WHERE parent_id = :parent_id:";

      $stmt = $this->_conn->newStatement($sql);
      $stmt->set('parent_id', $parent['id']);
    }
    else
    {
      $sql = "SELECT " . $this->_getSelectFields() . "
              FROM {$this->_node_table}
              WHERE path LIKE '{$parent['path']}%'
              AND id != {$parent['id']}";
      if($depth !=-1)
        $sql .= " AND level < ".($parent['level']+1+$depth);
      $sql .= " ORDER BY path";
      $stmt = $this->_conn->newStatement($sql);
    }

    return $stmt->getRecordSet();
  }

  function getChildrenAll($node)
  {
    $node = $this->_ensureNode($node);

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE path LIKE '{$node['path']}%'
            AND id != {$node['id']}
            ORDER BY path";

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getRecordSet();
  }

  function countChildren($node, $depth = 1)
  {
    $parent = $this->_ensureNode($node);

    if($depth == 1)
    {
      $sql = "SELECT count(id) as counter FROM {$this->_node_table}
              WHERE parent_id = :parent_id:";

      $stmt = $this->_conn->newStatement($sql);
      $stmt->set('parent_id', $parent['id']);
    }
    else
    {
      $sql = "SELECT count(id) as counter
              FROM {$this->_node_table}
              WHERE path LIKE '{$parent['path']}%'
              AND id != {$parent['id']}";
      if($depth !=-1)
        $sql .= " AND level < ".($parent['level']+1+$depth);
      $stmt = $this->_conn->newStatement($sql);
    }
    return $stmt->getOneValue();
  }

  function countChildrenAll($node)
  {
    $parent = $this->_ensureNode($node);

    $sql = "SELECT count(id) as counter
            FROM {$this->_node_table}
            WHERE path LIKE '{$parent['path']}%'
            AND id != {$parent['id']}";

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
      if(isset($node['id']))
        $id = $node['id'];
      else
        return null;
    }
    elseif(is_object($node))
    {
      if(!$id = $node->get('id'))
        return null;
    }
    else
      $id = $node;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE id=:id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger('id', $id);

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
    $parent_id = null;
    $path_to_node = '';

    foreach($rs as $node)
    {
      if($node['level'] < $curr_level)
        continue;

      if($node['identifier'] == $path_array[$curr_level] &&
         (!$parent_id ||
         $node['parent_id'] == $parent_id))
      {
        $parent_id = $node['id'];

        $curr_level++;
        $path_to_node .= '/' . $node['identifier'];

        if($curr_level == $level)
          return $node;
      }
    }
    return null;
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
            WHERE " . $this->_dbIn('id', $ids) . "
            ORDER BY path";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function getMaxChildIdentifier($node)
  {
    $parent = $this->_ensureNode($node);

    $sql = "SELECT identifier FROM {$this->_node_table}
            WHERE
            parent_id=:parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setInteger('parent_id', $parent['id']);

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

    if(isset($values['identifier']))
    {
      if($node['parent_id'] == 0 && $values['identifier'])//root check
        throw new lmbConsistencyTreeException('Root node is forbidden to have an identifier');

      if($node['identifier'] != $values['identifier'])
        $this->_ensureUniqueSiblingIdentifier($values['identifier'], $node['parent_id']);
    }

    if($internal === false)
      $values = $this->_processUserValues($values);

    if(!$values)
      return;

    $this->_db_table->updateById($node['id'], $values);
  }

  function _getNextNodeInsertId()
  {
    //if field is autoincremented why do we need it?
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
    $new_values['id'] = $this->_getNextNodeInsertId();
    $new_values['path'] = '/' . $new_values['id'] . '/';
    $new_values['level'] = 0;
    $new_values['parent_id'] = 0;
    $new_values['children'] = 0;

    $this->_db_table->insert($new_values);

    return $new_values['id'];
  }

  protected function _ensureNode($node)
  {
    if(!$res = $this->getNode($node))
      throw new lmbInvalidNodeTreeException($node);
    return $res;
  }

  protected function _ensureUniqueSiblingIdentifier($identifier, $parent_id)
  {
    $sql = "SELECT identifier FROM {$this->_node_table}
            WHERE
            identifier=:identifier: AND
            parent_id=:parent_id:";

    $stmt = $this->_conn->newStatement($sql);
    $stmt->setVarChar('identifier', $identifier);
    $stmt->setInteger('parent_id', $parent_id);
    if($stmt->getOneRecord())
      throw new lmbConsistencyTreeException("There's already a sibling with such an identifier '$identifier'");
  }

  function createNode($node, $values)
  {
    $parent_node = $this->_ensureNode($node);

    $parent_id = $parent_node['id'];

    $new_values = $this->_processUserValues($values);

    if(!isset($new_values['identifier']) || $new_values['identifier'] == '')
      throw new lmbConsistencyTreeException("Identifier property is required");

    $this->_ensureUniqueSiblingIdentifier($new_values['identifier'], $parent_id);

    $new_values['id'] = $this->_getNextNodeInsertId();
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
    $node = $this->_ensureNode($node);

    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}
                                        WHERE
                                        path LIKE :path:");

    $stmt->setVarChar('path', $node['path'] . '%');
    $stmt->execute();

    $stmt = $this->_conn->newStatement("UPDATE {$this->_node_table}
                                        SET children = children - 1
                                        WHERE
                                        id = :id:");

    $stmt->setInteger('id', $node['parent_id']);

    $stmt->execute();
  }

  function deleteAll()
  {
    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}");
    $stmt->execute();
  }

  function moveNode($source_node, $target_node)
  {
    if($source_node == $target_node)
      throw new lmbConsistencyTreeException("Can not move node into itself('$source_node')");

    $source_node = $this->_ensureNode($source_node);
    $target_node = $this->_ensureNode($target_node);

    if(strstr($target_node['path'], $source_node['path']) !== false)
      throw new lmbConsistencyTreeException("Can not parent node('$source_node') into child node('$target_node')");

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
            level = level + {$target_node['level']} - {$source_node['level']} + 1
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
  }
}

?>
