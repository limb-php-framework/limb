<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/wact/src/components/WactClassPath.class.php');

/**
 * abstract class WactBaseIteratorComponent.
 *
 * @package wact
 * @version $Id$
 */
abstract class WactBaseIteratorComponent extends WactRuntimeComponent
{
  protected $decorators = array();
  protected $order_params = array();
  protected $targets = array();
  protected $navigator_id;
  protected $only_first_record;
  protected $offset = 0;
  protected $limit;

  protected $buffers = array();

  function setOnlyFirstRecord($only_first_record = true)
  {
    $this->only_first_record = $only_first_record;
  }

  function setOffset($offset)
  {
    $this->offset = $offset;
  }

  function setLimit($limit)
  {
    $this->limit = $limit;
  }

  function setNavigator($navigator_id)
  {
    $this->navigator_id = $navigator_id;
  }

  function addDataSetDecorator($class_name, $include_path = '')
  {
    $this->decorators[$class_name] = array('class_name' => $class_name,
                                           'include_path' => $include_path,
                                           'params' => array());
  }

  function setOrder($order)
  {
    if(is_array($order))
    {
      $this->order_params = $order;
      return;
    }
    else
      $this->order_params = self :: extractOrderPairsFromString($order);
  }

  function addDataSetDecoratorParameter($class_name, $name, $value)
  {
    $this->decorators[$class_name]['params'][$name] = $value;
  }

  function addBuffer($component, $var_name = null)
  {
    $this->buffers[] = array($component, $var_name);
  }

  function process()
  {
    $dataset = $this->getDataset();

    if(!is_object($dataset))
      throw new WactException('Expected a dataset object');

    // process a special case when dataset is not an iterator but some other object
    if(!method_exists($dataset, 'count'))
    {
      $this->_passRecordToBuffer($dataset);
      return;
    }

    if(is_array($this->order_params) && count($this->order_params) && method_exists($dataset, 'sort'))
      $dataset->sort($this->order_params);

    if(($this->offset || $this->limit) && method_exists($dataset, 'paginate'))
    {
      if(!$this->limit)
        $this->limit = $dataset->count();

      $dataset->paginate($this->offset, $this->limit);
    }

    if($this->navigator_id)
      $this->_applyNavigator($dataset);

    $dataset = $this->_applyDecorators($dataset);

    if($this->only_first_record)
      $this->_processForFirstRecord($dataset);
    else
      $this->_processForFullDataSet($dataset);
  }

  abstract function getDataset();

  protected function _processForFirstRecord($dataset)
  {
    if(method_exists($dataset, 'paginate'))
      $dataset->paginate(0, 1);

    $dataset->rewind();
    if($dataset->valid())
      $record = $dataset->current();
    else
      $record = array();

    $this->_passRecordToBuffer($record);
  }

  protected function _passRecordToBuffer($record)
  {
    foreach($this->buffers as $buffer)
    {
      list($component, $var_name) = $buffer;

      if($var_name)
      {
        $component->set($var_name, $record);
      }
      else
      {
        if(!method_exists($component, 'registerDatasource'))
        {
          throw new WactException('Target component does not accept datasource',
                                array('target_component_class' => get_class($component),
                                      'target_component_id' => $component->getId()));
        }

        $component->registerDatasource($record);
      }
    }
  }

  protected function _processForFullDataSet($dataset)
  {
    $this->_passDatasetToBuffer($dataset);
  }

  protected function _passDatasetToBuffer($dataset)
  {
    foreach($this->buffers as $buffer)
    {
      list($component, $var_name) = $buffer;

      if($var_name)
      {
        $component->set($var_name, $dataset);
      }
      else
      {
        if(!method_exists($component, 'registerDataset'))
        {
          throw new WactException('Target component does not accept dataset',
                                array('target_component_class' => get_class($component),
                                      'target_component_id' => $component->getId()));
        }

        $component->registerDataset($dataset);
      }
    }
  }

  protected function _applyDecorators($dataset)
  {
    foreach($this->decorators as $decorator_class => $decorator_info)
    {
      $class_path = new WactClassPath($decorator_info['class_name'], $decorator_info['include_path']);
      $dataset = $class_path->createObject(array($dataset));
      $this->_addParamsToDataset($dataset, $decorator_info['params']);
    }
    return $dataset;
  }

  protected function _addParamsToDataset($dataset, $params)
  {
    foreach($params as $param => $value)
    {
      $method = WactTemplate :: toStudlyCaps('set_' . $param);
      $dataset->$method($value);
    }
  }

  protected function _applyNavigator($dataset)
  {
    if(($navigator = $this->_getNavigatorComponent())  && method_exists($dataset, 'paginate'))
    {
      $navigator->setPagedDataset($dataset);
      $dataset->paginate($navigator->getStartingItem(), $navigator->getItemsPerPage());
    }
  }

  protected function _getNavigatorComponent()
  {
    if(!$this->navigator_id)
      return null;

    if(!$navigator = $this->parent->findChild($this->navigator_id))
      throw new WactException('Navigator component not found', array('navigator' => $this->navigator_id));

    return $navigator;
  }

  static function extractOrderPairsFromString($order_string)
  {
    $order_items = explode(',', $order_string);
    $order_pairs = array();
    foreach($order_items as $order_pair)
    {
      $arr = explode('=', $order_pair);

      if(isset($arr[1]))
      {
        if(strtolower($arr[1]) == 'asc' || strtolower($arr[1]) == 'desc'
           || strtolower($arr[1]) == 'rand()' || strtolower($arr[1]) == 'random()')
          $order_pairs[$arr[0]] = strtoupper($arr[1]);
        else
          throw new WactException('Wrong order type', array('order' => $arr[1]));
      }
      else
        $order_pairs[$arr[0]] = 'ASC';
    }

    return $order_pairs;
  }
}

