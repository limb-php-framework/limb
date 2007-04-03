<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTableRecordsFetcher.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbQueryBasedFetcher.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');

class lmbTableRecordsFetcher extends lmbQueryBasedFetcher
{
  protected $table_class;
  protected $table_name;
  protected $ids = array();
  protected $field_name = 'id';

  function __construct($table_class = '')
  {
    if($table_class)
      $this->table_class = $table_class;
  }

  function setTableClass($table_class)
  {
    $this->table_class = $table_class;
  }

  function setTableName($table_name)
  {
    $this->table_name = $table_name;
  }

  function setFieldName($value)
  {
    $this->field_name = $value;
  }

  function setIds($value)
  {
    $this->ids = $value;
  }

  function _createQuery()
  {
    $db_table = $this->_createDbTable();

    return $db_table->getSelectQuery();
  }

  protected function _collectModifiers()
  {
    if($this->ids)
    {
      $criteria = new lmbSQLFieldCriteria($this->field_name, $this->ids, lmbSQLFieldCriteria :: IN);
      $this->addQueryModifier(new lmbCriteriaQueryModifier($criteria));
    }
  }

  protected function _createDbTable()
  {
    if($this->table_class)
    {
      $class_path = new lmbClassPath($this->table_class);
      return $class_path->createObject();
    }
    elseif($this->table_name)
    {
      return new lmbTableGateway($this->table_name);
    }
  }
}
?>
