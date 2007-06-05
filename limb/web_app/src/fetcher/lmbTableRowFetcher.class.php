<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');

class lmbTableRowFetcher extends lmbFetcher
{
  protected $table_class;
  protected $table_name;
  protected $field = 'id';
  protected $value = null;

  function setTableClass($table_class)
  {
    $this->table_class = $table_class;
  }

  function setTableName($table_name)
  {
    $this->table_name = $table_name;
  }

  function setField($field)
  {
    $this->field = $field;
  }

  function setValue($value)
  {
    $this->value = $value;
  }

  function _createDataSet()
  {
    if($this->value == null)
      return new lmbCollection();

    $toolkit = lmbToolkit :: instance();
    $db_table = $this->_createDbTable();

    return $db_table->select(new lmbSQLFieldCriteria($this->field, $this->value));
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
