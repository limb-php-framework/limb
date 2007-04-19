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
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/tree/src/tree/lmbTree.interface.php');

class lmbMPTree implements lmbTree
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
    $sql_exec_fields = array();
    foreach($this->_params as $name)
    {
      $sql_exec_fields[] = $this->_node_table . '.' . $name . ' AS ' . $name;
    }

    return implode(', ', $sql_exec_fields);
  }

  function _processUserValues($values)
  {
    if($this->_dumb_mode)
      return $values;

    $processed = array();
    foreach($values as $field => $value)
    {
      if(!in_array($field, $this->_params))
        continue;

      if(in_array($field, $this->_required_params))
        continue;

      $processed[$field] = $value;
    }
    return $processed;
  }

  function getTopNodes()
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE level=1";
    $stmt = $this->_conn->newStatement($sql);

    if($root_node = $stmt->getOneRecord())
      return $root_node;

    return false;
  }

  function getParents($node)
  {
    if(!$child = $this->getNode($node))
      return null;

    if($child['level'] < 1)
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
    if(!$child = $this->getNode($node))
      return null;

    if($child['id'] == $child['root_id'])
      return null;

    return $this->getNode($child['parent_id']);
  }

  function getSiblings($node)
  {
    if(!$me = $this->getNode($node))
      return null;

    if(!$me['parent_id'])
      return new lmbCollection(array($me));
    return $this->getChildren($me['parent_id']);
  }

  function getChildren($node, $depth = 1)
  {
    if(!$parent = $this->getNode($node))
      return null;

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
    if(!$node = $this->getNode($node))
      return null;

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
    if(!$parent = $this->getNode($node))
      return null;

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
    if(!$parent = $this->getNode($node))
      return null;

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
      return $node;
    elseif(is_object($node))
      $id = $node->get('id');
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
    if(!$node = $this->getNode($node))
      return null;

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
    if(!($parent = $this->getNode($node)))
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

    if(isset($values['parent_id']) && $node['parent_id'] != $values['parent_id'])
      $this->moveNode($id, $values['parent_id']);

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

  protected function _createRootNode()
  {
    $new_values['id'] = $this->_getNextNodeInsertId();
    $new_values['path'] = '/' . $new_values['id'] . '/';
    $new_values['root_id'] = $new_values['id'];
    $new_values['level'] = 0;
    $new_values['parent_id'] = 0;
    $new_values['children'] = 0;

    $this->_db_table->insert($new_values);

    return $new_values['id'];
  }

  function createNode($node, $values)
  {
    if(!$parent_node = $this->getNode($node))
      return false;

    $parent_id = $parent_node['id'];

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
    if(!$node = $this->getNode($node))
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

  function moveNode($source_node, $target_node)
  {
    if($source_node == $target_node)
      return false;

    if(!$source_node = $this->getNode($source_node))
      return false;

    if(!$target_node = $this->getNode($target_node))
      return false;

    if(strstr($target_node['path'], $source_node['path']) !== false)
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
