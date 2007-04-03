<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFetcher.class.php 5382 2007-03-28 12:54:38Z pachanga $
 * @package    web_app
 */
lmb_require('limb/dbal/src/modifier/lmbOrderQueryModifier.class.php');
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');

abstract class lmbFetcher
{
  protected $decorators = array();
  protected $order_params = array();
  protected $offset = 0;
  protected $limit = 0;

  function __construct()
  {
    $this->_collectDecorators();
  }

  function setOffset($offset)
  {
    $this->offset = $offset;
  }

  function setLimit($limit)
  {
    $this->limit = $limit;
  }

  function setOrder($order)
  {
    if(is_array($order))
    {
      $this->order_params = $order;
      return;
    }
    else
      $this->order_params = lmbOrderQueryModifier :: extractOrderPairsFromString($order);
  }

  function addDecorator($decorator, $params = array())
  {
    $this->decorators[] = array($decorator, $params);
  }

  protected function _applyDecorators($dataset)
  {
    $toolkit = lmbToolkit :: instance();

    foreach($this->decorators as $decorator_data)
    {
      $class_path = new lmbClassPath($decorator_data[0]);
      $dataset = $class_path->createObject(array($dataset));
      $this->_addParamsToDataset($dataset, $decorator_data[1]);
    }
    return $dataset;
  }

  protected function _addParamsToDataset($dataset, $params)
  {
    foreach($params as $param => $value)
    {
      $method = lmb_camel_case('set_'.$param, false);
      $dataset->$method($value);
    }
  }

  protected function _collectDecorators(){}

  function fetch()
  {
    $res = $this->_createDataSet();

    if(is_array($res))
      $dataset = new lmbPagedArrayDataset($res);
    elseif(is_object($res))
      $dataset = $res;
    else
      $dataset = new lmbPagedArrayDataset();

    $dataset = $this->_applyDecorators($dataset);
    if(is_array($this->order_params) && count($this->order_params))
      $dataset->sort($this->order_params);

    if($this->offset || $this->limit)
    {
      if(!$this->limit)
        $this->limit = $dataset->count();

      $dataset->paginate($this->offset, $this->limit);
    }
    return $dataset;
  }

  function fetchOne()
  {
    $dataset = $this->fetch();
    $dataset->rewind();
    if($dataset->valid())
      return $dataset->current();
  }

  /**
   * @deprecated
   * @see fetch()
   */
  function getDataSet()
  {
    return $this->fetch();
  }

  /**
   * @deprecated
   * @see fetchOne()
   */
  function getFirstRecord()
  {
    return $this->fetchOne();
  }
}
?>
