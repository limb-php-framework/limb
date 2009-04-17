<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbCriteriaQuery.class.php');

/**
 * class lmbDeleteQuery.
 *
 * @package dbal
 * @version $Id: lmbDeleteQuery.class.php 7890 2009-04-17 14:55:29Z vasiatka $
 */
class lmbDeleteQuery extends lmbCriteriaQuery
{
  protected $_table;

  function __construct($table, $conn=null)
  {
    $this->_table = $table;
    parent :: __construct("DELETE FROM %table% %where%", $conn);
    $this->_registerHint('table');
  }

  function getTable()
  {
    return $this->_table;
  }

  protected function _getTableHint()
  {
    return $this->_conn->quoteIdentifier($this->_table);
  }
}

