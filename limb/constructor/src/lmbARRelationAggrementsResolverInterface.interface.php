<?php
interface lmbARRelationAggrementsResolverInterface
{
  function isRelationColumn($column);  
  function isRelationColumnHasOne($column);
  
  function makeTableNameFromRelationColumn($column);
  function makeRelationColumnNameFromTable($table);
  function makeCollectionNameForTable($table);
}