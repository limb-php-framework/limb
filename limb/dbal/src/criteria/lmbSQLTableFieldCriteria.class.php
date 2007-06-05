<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbSQLTableFieldCriteria extends lmbSQLFieldCriteria
{
  protected $table;

  function __construct($raw_column, $value, $comparison = null)
  {
    @list($table, $column) = explode('.', $raw_column);

    if($column === null)
      throw new lmbException('Column must contain table name', array('column' => $raw_column));

    parent :: __construct($raw_column, $value, $comparison);

    $this->table = $table;
  }

  function getTable()
  {
    return $this->table;
  }
}

?>
