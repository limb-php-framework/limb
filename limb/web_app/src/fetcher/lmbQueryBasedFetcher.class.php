<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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
