<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbQueryBasedFetcher.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/dbal/src/modifier/lmbOrderQueryModifier.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/dbal/src/modifier/lmbCriteriaQueryModifier.class.php');

class lmbQueryBasedFetcher extends lmbFetcher
{
  protected $query;
  protected $modifiers = array();

  function __construct($query = '')
  {
    $this->query = $query;

    parent :: __construct();
  }

  function addQueryModifier($modifier)
  {
    $this->modifiers[] = $modifier;
  }

  function setOrder($order_string)
  {
    $this->addQueryModifier(new lmbOrderQueryModifier($order_string));
  }

  function setQueryName($query_name)
  {
    $this->query = $query_name;
  }

  protected function _applyModifiers($query)
  {
    foreach($this->modifiers as $modifier)
      $modifier->applyTo($query);
  }

  protected function _createQuery()
  {
    if(is_object($this->query))
      return clone($this->query);

    $class_path = new lmbClassPath($this->query);
    return  $class_path->createObject(array(lmbToolkit :: instance()->getDefaultDbConnection()));
  }

  protected function _collectModifiers(){}

  protected function _createDataSet()
  {
    $this->_collectModifiers();
    $query = $this->_createQuery();
    $this->_applyModifiers($query);
    return $query->getRecordSet();
  }

  function addFalseCriteriaModifier()
  {
    $this->addQueryModifier(new lmbCriteriaQueryModifier(new lmbSQLFalseCriteria()));
  }
}
?>
