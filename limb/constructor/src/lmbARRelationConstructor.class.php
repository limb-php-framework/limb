<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 *
 *@todo add proper relation resolving by acceptions
 *@todo add verbosity to variables names
 */
class lmbARRelationConstructor
{
  protected $_schema_info = array();

  /**
   * @var lmbDbInfo
   */
  protected $_db_info;

  /**
   * @var lmbARRelationAggrementsResolverInterface
   */
  protected $_aggrements_resolver;

  protected $_columns_map;

  var $messages = array();

  function __construct($db_info, $relations_resolver)
  {
    $this->_db_info = $db_info;
    $this->_aggrements_resolver = $relations_resolver;
  }

  function _getColumnsMap()
  {
    if(is_null($this->_columns_map))
    {
      $columns = array();
      foreach ($this->_db_info->getTables() as $table)
      {
        foreach($table->getColumns() as $column)
        {
          if(!isset($columns[$column->getName()]))
            $columns[$column->getName()] = array();

          $columns[$column->getName()][] = $column;
        }
      }
      $this->_columns_map = $columns;
    }

    return $this->_columns_map;
  }

  function getColumnsFromAllTablesByName($column_name)
  {
    $columns = $this->_getColumnsMap();

    if(!array_key_exists($column_name, $columns))
      return array();

    return $columns[$column_name];
  }

  /**
   * @param lmbDbColumnInfo $relation_column
   * @return array
   */
  function _formRelationHasOne($relation_column)
  {
    $column_name = $relation_column->getName();

    $child_table = $this->_aggrements_resolver->makeTableNameFromRelationColumn($relation_column);

    if(!$this->_db_info->hasTable($child_table))
    {
      $this->addMessage("Child table '$child_table' for column '$column_name' not found in schema");
      return array();
    }

    $form_relation = array(
      $child_table => array(
        'field' => $column_name,
        'class' => lmb_camel_case($child_table, true)
    ));

    if($relation_column->isNullable())
    {
      $form_relation[$child_table]['cascade_delete'] = false;
      $form_relation[$child_table]['can_be_null'] = true;
    }

    return $form_relation;
  }

  function getRelationsHasOneFor($table_name)
  {
    $table = $this->_db_info->getTable($table_name);

    $relations = array();
    foreach ($table->getColumns() as $column)
    {
      if(!$this->_aggrements_resolver->isRelationColumnHasOne($column))
        continue;

      $relations = array_merge(
          $relations,
          $this->_formRelationHasOne($column)
      );
    }

    return $relations;
  }

  function _formRelationBelongsTo($relation_column)
  {
    return array(
      $relation_column->getTable()->getName() => array(
        'field' => $relation_column->getName(),
        'class' => lmb_camel_case($relation_column->getTable()->getName(), true))
    );
  }

  function getRelationsBelongsFor($table_name)
  {
    $table = $this->_db_info->getTable($table_name);

    $relation_column_name = $this->_aggrements_resolver->makeRelationColumnNameFromTable($table);

    $relations = array();
    foreach($this->getColumnsFromAllTablesByName($relation_column_name) as $column)
    {
      if($this->_aggrements_resolver->isParentTableBelongsTo($column->getTable(), $table))
        $relations = array_merge(
          $relations,
          $this->_formRelationBelongsTo($column)
        );
    }

    return $relations;
  }

  function _formRelationHasMany($relation_column)
  {
    $child_table_name = $relation_column->getTable()->getName();
    $relation_name = $this->_aggrements_resolver->makeCollectionNameForTable($child_table_name);

    $form_relation = array(
      $relation_name => array(
        'field' => $relation_column->getName(),
        'class' => lmb_camel_case($child_table_name))
    );

    if($relation_column->isNullable())
      $form_relation[$relation_name]['nullify'] = true;

    return $form_relation;
  }

  function getRelationsHasManyFor($table_name)
  {
    $table = $this->_db_info->getTable($table_name);

    $relation_column_name = $this->_aggrements_resolver->makeRelationColumnNameFromTable($table);

    $relations = array();
    foreach($this->getColumnsFromAllTablesByName($relation_column_name) as $column)
    {
      if($this->_aggrements_resolver->isManyToManyRelationTable($column->getTable()))
        continue;

      if($this->_aggrements_resolver->isRelationColumnHasOne($column))
        continue;

      $relations = array_merge(
        $relations,
        $this->_formRelationHasMany($column)
      );
    }
    return $relations;
  }

  function _formRelationManyBelongsTo($relation_column)
  {
    $relation_column_name = $relation_column->getName();

    $parent_table_name = $this->_aggrements_resolver->makeTableNameFromRelationColumn($relation_column_name);

    if(!$this->_db_info->hasTable($parent_table_name))
    {
      $this->addMessage("Parent table '$parent_table_name' for column '$relation_column_name' not found in schema");
      return array();
    }

    $form_relation = array($parent_table_name => array(
        'field' => $relation_column_name,
        'class' => lmb_camel_case($parent_table_name, true),
        ));

    if($relation_column->isNullable())
      $form_relation[$parent_table_name]['can_be_null'] = true;

    return $form_relation;
  }

  function getRelationsManyBelongsFor($table_name)
  {
    $table = $this->_db_info->getTable($table_name);

    if($this->_aggrements_resolver->isManyToManyRelationTable($table))
      return array();

    $relations = array();
    foreach ($table->getColumns() as $column)
    {
      if($this->_aggrements_resolver->isRelationColumnHasOne($column))
        continue;

      if($this->_aggrements_resolver->isRelationColumn($column->getName()))
        $relations = array_merge(
          $relations,
          $this->_formRelationManyBelongsTo($column));
    }

    return $relations;
  }

  function _findManyToManyRelationTableFor($source_table)
  {
    $relation_tables = array();
    foreach ($this->_db_info->getTables() as $relation_table)
    {
      if($this->_aggrements_resolver->isManyToManyRelationTableFor($source_table, $relation_table))
        $relation_tables[] = $relation_table;
    }

    return $relation_tables;
  }

  function _formRelationManyToMany($local_table, $relation_table)
  {
    $local_relation_column_name = $this->_aggrements_resolver->makeRelationColumnNameFromTable($local_table->getName());

    $related_tables = $this->_aggrements_resolver->getManyToManyRelatedTablesFromRelationTable($relation_table);

    $foreign_table = ($related_tables[0] == $local_table->getName())
      ? $this->_db_info->getTable($related_tables[1])
      : $this->_db_info->getTable($related_tables[0]);

    return array(
      lmb_plural($foreign_table->getName()) => array(
        'field' => $local_relation_column_name,
        'foreign_field' => $this->_aggrements_resolver->makeRelationColumnNameFromTable($foreign_table),
        'table' => $relation_table->getName(),
        'class' => lmb_camel_case($foreign_table->getName()))
    );
  }

  function getRelationsManyToManyFor($table_name)
  {
    $table = $this->_db_info->getTable($table_name);

    $relation_tables = $this->_findManyToManyRelationTableFor($table);

    $relations = array();
    foreach($relation_tables as $relation_table)
    {
      $relations = array_merge(
        $relations,
        $this->_formRelationManyToMany($table, $relation_table)
      );
    }
    return $relations;
  }

  function addMessage($message)
  {
    $this->messages[] = $message;
  }
}