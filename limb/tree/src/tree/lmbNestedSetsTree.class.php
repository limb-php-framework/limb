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

/**
 * Base class implementing a Nested Sets approach to storing tree-like structures in database tables.
 */
class lmbNestedSetsTree implements lmbTree
{
  protected $_conn = null;

  protected $_params = array();

  protected $_required_params = array();
  protected $_left;
  protected $_right;
  protected $_level;

  protected $_db_table = false;

  function __construct($node_table = 'ns_tree', $left = 'c_left', $right = 'c_right', $level = 'c_level')
  {
    $toolkit = lmbToolkit :: instance();

    $this->_conn = $toolkit->getDefaultDbConnection();
    $this->_node_table = $node_table;
    $this->_left = $left;
    $this->_right = $right;
    $this->_level = $level;
    $this->_required_params = array('id', $left, $right, $level);
    $this->_db_table = new lmbTableGateway($this->_node_table);
    $this->_params = $this->_db_table->getColumnNames();
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

    $sql_exec_fields = array();
    foreach($this->_params as $name)
    {
      $sql_exec_fields[] = $table . '.' . $name . ' AS ' . $name;
    }

    return implode(', ', $sql_exec_fields);
  }

  function _processUserValues($values)
  {

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

  function initTree()
  {
    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}");
    $stmt->execute();

    $values = array();
    $values['id'] = $this->_getNextNodeInsertId();
    $values[$this->_left] = 1;
    $values[$this->_right] = 2;
    $values[$this->_level] = 0;

    $this->_db_table->insert($values);

    return $values['id'];
  }

  function getTopNodes()
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE {$this->_level}=1";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  function getParents($node)
  {
    if(!$child = $this->getNode($node))
      return null;

    if($child[$this->_level] < 1)
      return null;

   $sql =  "SELECT " . $this->_getSelectFields() . "
            FROM  {$this->_node_table}
            WHERE {$this->_left} < {$child[$this->_left]}
            AND {$this->_right} >  {$child[$this->_right]}
            AND {$this->_level}>0";

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getRecordSet();
  }

  function getParent($node)
  {
    if(!$child = $this->getNode($node))
      return null;

    if($child[$this->_level] < 1)
      return null;
     $sql =  "SELECT " . $this->_getSelectFields() . "
              FROM  {$this->_node_table}
              WHERE {$this->_left} < {$child[$this->_left]}
              AND {$this->_right} >  {$child[$this->_right]}
              AND {$this->_level} = ".($child[$this->_level]-1);
    /*$sql =  "SELECT " . $this->_getSelectFields() . "
            FROM  {$this->_node_table} _{$this->_node_table},
                  {$this->_node_table}
            WHERE _{$this->_node_table}.id='{$child['id']}'
            AND _{$this->_node_table}.{$this->_left} BETWEEN {$this->_node_table}.{$this->_left} AND {$this->_node_table}.{$this->_right}
            AND {$this->_node_table}.{$this->_level}=_{$this->_node_table}.{$this->_level}-1";*/

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getOneRecord();
  }

  function getSiblings($node)
  {
    if(!($sibling = $this->getNode($node)))
      return null;

    $parent = $this->getParent($sibling['id']);
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
    if(!$parent = $this->getNode($node))
      return null;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE {$parent[$this->_left]} < {$this->_left}
            AND {$parent[$this->_right]} > {$this->_right}";
    if($depth!=-1)
      $sql .= " AND {$this->_level} <= ".($parent[$this->_level]+$depth);

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
    if(!$parent = $this->getNode($node))
      return null;

    $sql = "SELECT count(id) as counter
            FROM {$this->_node_table}
            WHERE {$parent[$this->_left]} < {$this->_left}
            AND {$parent[$this->_right]} > {$this->_right}";

    if($depth!=-1)
      $sql .= " AND {$this->_level} <= ".($parent[$this->_level]+$depth);

    $stmt = $this->_conn->newStatement($sql);

    return $stmt->getOneValue();
  }

/*  function getSubBranch($node, $depth = -1, $include_parent = false)
  {
    if(!$parent_node = $this->getNode($node))
      return null;

    $id = $parent_node['id'];

    if($depth != -1)
      $depth_condition = " AND {$this->_level} <=" . ($parent_node[$this->_level] + $depth);
    else
      $depth_condition = '';

    if($include_parent)
      $include_parent_condition = '';
    else
      $include_parent_condition = " AND id!={$id}";

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE {$parent_node[$this->_left]} <= {$this->_left}
            AND {$parent_node[$this->_right]} >= {$this->_right}
            {$depth_condition}
            {$include_parent_condition}
            ORDER BY {$this->_left}";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }*/

  function getNode($node)
  {
    if(is_array($node) or is_object($node))
      return $node;

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

    if(reset($path_array) == '')
      array_shift($path_array);

    if(end($path_array) == '')
      array_pop($path_array);

    $level = sizeof($path_array);

    if(!count($path_array))
      return null;

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE {$this->_level}=0";
    $stmt = $this->_conn->newStatement($sql);

    if(!$root_node = $stmt->getOneRecord())
      return null;

    $t='';
    $w='t0.id='.$root_node['id'];
    for($i=0;$i<count($path_array);$i++)
    {
      $c=$i+1;
      $t.=",\n{$this->_node_table} t".$c;
      $w.=" AND t".$c.".identifier='".addslashes($path_array[$i])."'
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
    if(!$node = $this->getNode($node))
      return null;

    $path = '';

    if(!$parents = $this->getParents($node))
      return $path .= $delimeter . $node['identifier'];

    foreach($parents as $parent)
      $path .= $delimeter . $parent['identifier'];

    return $path .= $delimeter . $node['identifier'];
  }

  function getNodesByIds($ids)
  {
    if(!$ids)
      return new lmbCollection();

    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table}
            WHERE " . $this->_dbIn('id', $ids) . "
            ORDER BY cleft";

    $stmt = $this->_conn->newStatement($sql);
    return $stmt->getRecordSet();
  }


  function isNode($id)
  {
    return ($this->getNode($id) !== null);
  }

  function _getNextNodeInsertId()
  {
    $sql = 'SELECT MAX(id) as m FROM '. $this->_node_table;
    $stmt = $this->_conn->newStatement($sql);
    $max = $stmt->getOneValue();

    return isset($max) ? $max + 1 : 1;
  }

  function _dbIn($column_name, $values)
  {
    $in_ids = implode("','", $values);
    return $column_name . " IN ('" . $in_ids . "')";
  }

  function createNode($values, $parent_node = null)
  {
    if(!$parent_node)
      return $this->_createRootNode($values);
    else
      return $this->_createSubNode($parent_node, $values);
  }

  protected function _createRootNode($values)
  {
    $sql = "SELECT " . $this->_getSelectFields() . "
            FROM {$this->_node_table} WHERE {$this->_level}=0";
    $stmt = $this->_conn->newStatement($sql);

    if($root_node = $stmt->getOneRecord())
      return $this->_createSubNode($root_node, $values);

    return flase;
  }

  protected function _createSubNode($node, $values)
  {
    if(!$parent_node = $this->getNode($node))
      return false;

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

    $new_values[$this->_left] = $parent_node[$this->_right];
    $new_values[$this->_right] = $parent_node[$this->_right]+1;
    $new_values[$this->_level] = $parent_node[$this->_level]+1;

    // creating a place for the record being inserted
    $sql = "UPDATE {$this->_node_table}
            SET {$this->_left}=IF( {$this->_left}>{$parent_node[$this->_right]}, {$this->_left}+2, {$this->_left}),
                {$this->_right}=IF( {$this->_right}>={$parent_node[$this->_right]},{$this->_right}+2,{$this->_right})
            WHERE {$this->_right}>={$parent_node[$this->_right]}";
    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    $this->_db_table->insert($new_values);

    return $new_values['id'];
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

    $this->_db_table->updateById($id, $values);

    return true;
  }

  function deleteNode($node)
  {
    if(!$node = $this->getNode($node))
      return false;

    $stmt = $this->_conn->newStatement("DELETE FROM {$this->_node_table}
                                        WHERE
                                        {$this->_left} BETWEEN  {$node[$this->_left]} AND {$node[$this->_right]}");
    $stmt->execute();

    $delta = ($node[$this->_right] - $node[$this->_left])+1;
    $sql = "UPDATE {$this->_node_table}
            SET {$this->_left}=IF({$this->_left}>{$node[$this->_left]}, {$this->_left}-{$delta}, {$this->_left}),
                {$this->_right}=IF({$this->_right}>{$node[$this->_left]}, {$this->_right}-{$delta}, {$this->_right})
            WHERE {$this->_right}>{$node[$this->_right]}";
    $stmt = $this->_conn->newStatement($sql);
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

    // whether it is being moved upwards along the path
    if($target_node[$this->_left] < $source_node[$this->_left] && $target_node[$this->_right] > $source_node[$this->_right] && $target_node[$this->_level] < $source_node[$this->_level] - 1 )
    {
      $sql = "UPDATE {$this->_node_table} SET
              {$this->_level}=IF({$this->_left} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, ".$this->_level.sprintf('%+d', -($source_node[$this->_level]-1)+$target_node[$this->_level]).", {$this->_level}),
              {$this->_right}=IF({$this->_right} BETWEEN ".($source_node[$this->_right]+1)." AND ".($target_node[$this->_right]-1).", {$this->_right}-".($source_node[$this->_right]-$source_node[$this->_left]+1).",
              IF({$this->_left} BETWEEN {$source_node[$this->_left]} AND ({$source_node[$this->_right]}), {$this->_right}+".((($target_node[$this->_right]-$source_node[$this->_right]-$source_node[$this->_level]+$target_node[$this->_level])/2)*2 + $source_node[$this->_level] - $target_node[$this->_level] - 1).",{$this->_right})),
              {$this->_left}=IF({$this->_left} BETWEEN ".($source_node[$this->_right]+1)." AND ".($target_node[$this->_right]-1).", {$this->_left}-".($source_node[$this->_right]-$source_node[$this->_left]+1).",
              IF({$this->_left} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, {$this->_left}+".((($target_node[$this->_right]-$source_node[$this->_right]-$source_node[$this->_level]+$target_node[$this->_level])/2)*2 + $source_node[$this->_level] - $target_node[$this->_level] - 1).", {$this->_left}))
              WHERE {$this->_left} BETWEEN ".($target_node[$this->_left]+1)." AND ".($target_node[$this->_right]-1);
    }
    elseif($target_node[$this->_left] < $source_node[$this->_left])
    {
       $sql = "UPDATE {$this->_node_table} SET
              {$this->_level}=IF({$this->_left} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, ".$this->_level.sprintf('%+d', -($source_node[$this->_level]-1)+$target_node[$this->_level]).', '.$this->_level."),
              {$this->_left}=IF($this->_left BETWEEN {$target_node[$this->_right]} AND ".($source_node[$this->_left]-1).", {$this->_left}+".($source_node[$this->_right]-$source_node[$this->_left]+1).",
              IF({$this->_left} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, {$this->_left}-".($source_node[$this->_left]-$target_node[$this->_right]).", {$this->_left})),
              {$this->_right}=IF({$this->_right} BETWEEN {$target_node[$this->_right]} AND {$source_node[$this->_left]}, {$this->_right}+".($source_node[$this->_right]-$source_node[$this->_left]+1).",
              IF({$this->_right} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, {$this->_right}-".($source_node[$this->_left]-$target_node[$this->_right]).", {$this->_right}))
              WHERE {$this->_left} BETWEEN {$target_node[$this->_left]} AND {$source_node[$this->_right]}
              OR {$this->_right} BETWEEN {$target_node[$this->_left]} AND {$source_node[$this->_right]}";

    }
    else
    {
       $sql = "UPDATE {$this->_node_table} SET
              {$this->_level}=IF({$this->_left} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, ".$this->_level.sprintf('%+d', -($source_node[$this->_level]-1)+$target_node[$this->_level]).", {$source_node[$this->_level]}),
              {$this->_left}=IF({$this->_left} BETWEEN {$source_node[$this->_right]} AND {$target_node[$this->_right]}, {$this->_left}-".($source_node[$this->_right]-$source_node[$this->_left]+1).",
              IF({$this->_left} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, {$this->_left}+".($target_node[$this->_right]-1-$source_node[$this->_right]).", {$this->_left})),
              {$this->_right}=IF({$this->_right} BETWEEN ".($source_node[$this->_right]+1)." AND ".($target_node[$this->_right]-1).", {$this->_right}-".($source_node[$this->_right]-$source_node[$this->_left]+1).",
              IF({$this->_right} BETWEEN {$source_node[$this->_left]} AND {$source_node[$this->_right]}, {$this->_right}+".($target_node[$this->_right]-1-$source_node[$this->_right]).", {$this->_right}))
              WHERE {$this->_left} BETWEEN {$source_node[$this->_left]} AND {$target_node[$this->_right]}
              OR {$this->_right} BETWEEN {$source_node[$this->_left]} AND {$target_node[$this->_right]}";
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
      $conditions = $this->_right.'='.($node[$this->_left]-1);
    else
      $conditions = $this->_left.'='.($node[$this->_right]+1);

    $sql = "SELECT {$this->_left}, {$this->_right}
            FROM {$this->_node_table}
            WHERE {$conditions} AND {$this->_level}={$node[$this->_level]}";
    $stmt =  $this->_conn->newStatement($sql);
    if(!$node2 = $stmt->getOneRecord())
      return false;

    $delta = ($step == 'up') ? ($node[$this->_left] - $node2[$this->_left]) : ($node2[$this->_left] - $node[$this->_left]);
    $delta2 = ($step == 'up') ? ($node[$this->_right] - $node2[$this->_right]) : ($node2[$this->_right] - $node[$this->_right]);

    $sql = "UPDATE {$this->_node_table} SET
            {$this->_right} = CASE
                  WHEN {$this->_left} BETWEEN {$node[$this->_left]} AND {$node[$this->_right]}
                    THEN ".( ($step == 'up') ? $this->_right.'-'.$delta : $this->_right.'+'.$delta2)."
                    ELSE ".( ($step == 'up') ? $this->_right.'+'.$delta2 : $this->_right.'-'.$delta)."
                  END,
            {$this->_left} = CASE
                  WHEN {$this->_left} BETWEEN {$node[$this->_left]} AND {$node[$this->_right]}
                    THEN ".( ($step == 'up') ? $this->_left.'-'.$delta : $this->_left.'+'.$delta2)."
                    ELSE ".( ($step == 'up') ? $this->_left.'+'.$delta2 : $this->_left.'-'.$delta)."
                  END
            WHERE ".(($step == 'up') ? $this->_left.'>='.$node2[$this->_left].' AND '.$this->_right.'<='.$node[$this->_right] : $this->_left.'>='.$node[$this->_left].' AND '.$this->_right.'<='.$node2[$this->_right]);

    $stmt = $this->_conn->newStatement($sql);
    $stmt->execute();

    return true;
  }
}

?>
