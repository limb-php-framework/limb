<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLTableFieldCriteria.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/core/src/exception/lmbInvalidArgumentException.class.php');

class lmbSQLTableFieldCriteria extends lmbSQLFieldCriteria
{
  protected $table;

  function __construct($raw_column, $value, $comparison = null)
  {
    @list($table, $column) = explode('.', $raw_column);

    if($column === null)
      throw new lmbInvalidArgumentException('column must contain table name', array('column' => $raw_column));

    parent :: __construct($raw_column, $value, $comparison);

    $this->table = $table;
  }

  function getTable()
  {
    return $this->table;
  }
}

?>
