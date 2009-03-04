<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactSelectOptionsSourceComponent.
 *
 * @package wact
 * @version $Id: WactSelectOptionsSourceComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactSelectOptionsSourceComponent extends WactDatasourceRuntimeComponent
{
  protected $field_for_id;
  protected $field_for_name;
  protected $render_default_option = false;
  protected $default_value;
  protected $default_name;
  protected $dataset;

  protected $choices = null;

  function useAsName($name)
  {
    $this->field_for_name = $name;
  }

  function useAsId($id)
  {
    $this->field_for_id = $id;
  }

  function setDefaultValue($value)
  {
    $this->render_default_option = true;
    $this->default_value = $value;
  }

  function setDefaultName($name)
  {
    $this->render_default_option = true;
    $this->default_name = $name;
  }

  function getChoices()
  {
    if(!is_null($this->choices))
      return $this->choices;

    $result = array();

    $this->_addDefaultOption($result);

    $this->choices = $this->_getArrayOfOptions($result);

    return $this->choices;
  }

  function _getArrayOfOptions(&$choices)
  {
    if($this->dataset)
      return $this->_exportDataSetAsChoices($choices);

    if(is_array($this->datasource))
    {
      $this->dataset = $this->datasource;
      return $this->_exportDatasetAsChoices($choices);
    }

    if(is_object($this->datasource))
    {
      if(method_exists($this->datasource,  'export'))
      {
        $this->dataset = $this->datasource->export();
        return $this->_exportDatasetAsChoices($choices);
      }
      if($this->datasource instanceof Iterator)
      {
        $this->dataset = $this->datasource;
        return $this->_exportDatasetAsChoices($choices);
      }
    }
  }

  protected function _exportDatasetAsChoices(&$choices)
  {
    if(!$this->dataset)
      return array();

    foreach($this->dataset as $key => $record)
    {
      if(!is_array($record) && !is_object($record))
      {
        $choices[$key] = $record;
      }
      elseif(!$this->field_for_id || !$this->field_for_name)
      {
        $choices[key($record)] = current($record);
      }
      elseif($this->field_for_id && $this->field_for_name)
        $choices[$record[$this->field_for_id]] = $record[$this->field_for_name];
    }

    return $choices;
  }

  protected function _addDefaultOption(&$choices)
  {
    if($this->render_default_option)
      $choices[$this->default_value] = $this->default_name;
  }

  function registerDataSet($dataset)
  {
    $this->dataset = WactTemplate :: castToIterator($dataset);
  }
}

