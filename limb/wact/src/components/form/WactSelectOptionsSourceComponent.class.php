<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactSelectOptionsSourceComponent.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactSelectOptionsSourceComponent extends WactDatasourceRuntimeComponent
{
  protected $field_for_id;
  protected $field_for_name;
  protected $render_default_option = false;
  protected $default_value;
  protected $default_name;
  protected $dataset;

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
    $result = array();

    $this->_addDefaultOption($result);

    return $this->_getArrayOfOptions($result);
  }

  function _getArrayOfOptions(&$choices)
  {
    if($this->dataset)
      return $this->_exportDataSetAsChoices($choices);

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
        $raw = $record->export();
        $choices[key($raw)] = current($raw);
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
?>