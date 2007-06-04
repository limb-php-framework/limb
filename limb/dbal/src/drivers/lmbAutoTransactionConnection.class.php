<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAutoTransactionConnection.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/core/src/lmbDecorator.class.php');

lmbDecorator :: generate('lmbDbConnection', 'lmbDbConnectionDecorator');

class lmbAutoTransactionConnection extends lmbDbConnectionDecorator
{
  protected $modifying_statements = array('UPDATE',
                                          'DELETE',
                                          'INSERT',
                                          'CREATE',
                                          'ALTER',
                                          'DROP');//do we need more?
  protected $is_in_transaction = false;

  function newStatement($sql)
  {
    if($this->_isModifyingSQL($sql))
      $this->beginTransaction();

    return parent :: newStatement($sql);
  }

  protected function _isModifyingSQL($sql)
  {
    $sql_trimmed = ltrim($sql);

    foreach($this->modifying_statements as $stmt)
    {
      if(stripos($sql_trimmed, $stmt . ' ') === 0)
        return true;
    }
    return false;
  }

  function beginTransaction()
  {
    if($this->is_in_transaction)
      return;
    parent :: beginTransaction();
    $this->is_in_transaction = true;
  }

  function commitTransaction()
  {
    if($this->is_in_transaction)
    {
      parent :: commitTransaction();
      $this->is_in_transaction = false;
    }
  }

  function rollbackTransaction()
  {
    if($this->is_in_transaction)
    {
      parent :: rollbackTransaction();
      $this->is_in_transaction = false;
    }
  }

  function isInTransaction()
  {
    return $this->is_in_transaction;
  }
}

?>