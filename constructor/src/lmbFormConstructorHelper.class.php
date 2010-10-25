<?php

class lmbFormConstructorHelper
{
  protected $columns = array();

  function __construct(array $columns)
  {
    $aggrement_resolver = new lmbARRelationAggrementsResolver();
    foreach ($columns as $column)
    {
      if(
        !$aggrement_resolver->isRelationColumn($column)
        && !$aggrement_resolver->isPrimaryKey($column)
      )
        $this->columns[] = $column;
    }
  }

  function getColumnsNames()
  {
    $names = array();
    foreach($this->columns as $column)
      $names[] = $column->getName();

    return $names;
  }

  protected function _createFieldInput($column)
  {
    return '[[input id="%%%" name="%%%" type="text" title="%%%"/]]';
  }

  protected function _createFieldTextArea($column)
  {
    return '[[textarea id="%%%" name="%%%" type="text" title="%%%" cols="40" rows="'.ceil($column->getSize()/40).'"/]]';
  }

  protected function _createFieldCheckbox($column)
  {
    return '[[js_checkbox id="%%%" name="%%%"/]]';
  }

  protected function _createFieldDatetime($column)
  {
    return ' <? $value = $this->item->get("%%%") ? $this->item->get("%%%") : date(); ?> '.
           ' [[date3Select id="%%%" name="%%%" title="%%%" year_class="date3select" lang="ru" value="[$value]" format="%s" /]]';
  }

  protected function _createFieldWysiwyg($column)
  {
    return '[[wysiwyg id="%%%" name="%%%" width="100%" height="300px" title="%%%"/]]';
  }

  /**
   * @param lmbDbColumnInfo $column
   */
  protected function _createFormFieldHtmlWithoutColumnName($column)
  {
    if($column->getType() === lmbDbTypeInfo::TYPE_CLOB)
      return $this->_createFieldWysiwyg($column);

    if($column->getType() === lmbDbTypeInfo::TYPE_CHAR || $column->getType() === lmbDbTypeInfo::TYPE_VARCHAR)
    {
      if(255 >= $column->getSize())
        return $this->_createFieldInput($column);
      else
        return $this->_createFieldTextArea($column);
    }

    if( $column->getType() === lmbDbTypeInfo::TYPE_BIT ||
        $column->getType() === lmbDbTypeInfo::TYPE_BOOLEAN ||
        $column->getType() === lmbDbTypeInfo::TYPE_SMALLINT
    )
      return $this->_createFieldCheckbox($column);

    if( strstr($column->getName(), 'time') ||
        strstr($column->getName(), 'date')
    )
      return $this->_createFieldDatetime($column);

    return $this->_createFieldInput($column);
  }

  /**
   * @param lmbDbColumnInfo $column
   */
  protected function _createFormFieldHtml($column)
  {
    return str_replace(
      '%%%',
      $column->getName(),
      $this->_createFormFieldHtmlWithoutColumnName($column)
    );
  }

  function createFormFields()
  {
    $fields = array();
    foreach($this->columns as $column)
      $fields[$column->getName()] = $this->_createFormFieldHtml($column);

    return $fields;
  }
}