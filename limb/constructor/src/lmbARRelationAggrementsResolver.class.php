<?php
lmb_require('limb/constructor/src/lmbARRelationAggrementsResolverInterface.interface.php');

class lmbARRelationAggrementsResolver implements lmbARRelationAggrementsResolverInterface
{
  function isRelationColumn($column)
  {
    if(is_object($column))
      $column = $column->getName();

    return '_id' == substr($column, -3);
  }

  function isPrimaryKey($column)
  {
    if(is_object($column))
      $column = $column->getName();

    return 'id' === $column;
  }

  function makeTableNameFromRelationColumn($column)
  {
    if(is_object($column))
      $column = $column->getName();
    return substr($column, 0, -3);
  }

  function makeRelationColumnNameFromTable($table)
  {
    if(is_object($table))
      $table = $table->getName();

    return $table.'_id';
  }

  function makeCollectionNameForTable($table)
  {
    if(is_object($table))
      $table = $table->getName();

    return lmb_plural($table);
  }

  function isRelationColumnHasOne($column)
  {
    if(!$this->isRelationColumn($column))
      return false;

    if(!$index = $column->getTable()->getIndexForColumn($column))
      return false;

    if(lmbDbIndexInfo::TYPE_UNIQUE != $index->getType())
      return false;

    return true;
  }

  function getManyToManyRelatedTablesFromRelationTable($relation_table)
  {
    return explode('2', $relation_table->getName());
  }

  function isManyToManyRelationTable($table)
  {
    $table_name = $table->getName();
    $related_tables = $this->getManyToManyRelatedTablesFromRelationTable($table);

    if(2 !== count($related_tables))
      return false;

    return true;
  }

  function isManyToManyRelationTableFor($source_table, $relation_table)
  {
    $this->isManyToManyRelationTable($relation_table);

    return in_array(
        $source_table->getName(),
        $this->getManyToManyRelatedTablesFromRelationTable($relation_table)
      )
      && $relation_table->getName() != $source_table->getName();
  }

  function isParentTableBelongsTo($parent_table, $child_table)
  {
    $relation_column_name = $this->makeRelationColumnNameFromTable($child_table);

    if(!$column = $parent_table->getColumn($relation_column_name))
      return false;

    if(!$this->isRelationColumnHasOne($column))
      return false;

    return true;
  }

}