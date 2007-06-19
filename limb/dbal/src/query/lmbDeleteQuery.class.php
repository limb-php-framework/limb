<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/query/lmbCriteriaQuery.class.php');

/**
 * class lmbDeleteQuery.
 *
 * @package dbal
 * @version $Id: lmbDeleteQuery.class.php 6005 2007-06-19 21:14:49Z pachanga $
 */
class lmbDeleteQuery extends lmbCriteriaQuery
{
  protected $_table;

  function __construct($table, $conn)
  {
    $this->_table = $table;
    parent :: __construct("DELETE FROM %table% %where%", $conn);
  }

  protected function _getTableHint()
  {
    return $this->_conn->quoteIdentifier($this->_table);
  }
}
?>
