<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDeleteQuery.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/query/lmbCriteriaQuery.class.php');

class lmbDeleteQuery extends lmbCriteriaQuery
{
  protected $_table;

  function __construct($table, $conn)
  {
    $this->_table = $table;
    parent :: __construct("DELETE FROM %table% %where%", $conn);
  }

  protected function _declareHints()
  {
    return array('table', 'where');
  }

  protected function _getTableHint()
  {
    return $this->_conn->quoteIdentifier($this->_table);
  }
}
?>
