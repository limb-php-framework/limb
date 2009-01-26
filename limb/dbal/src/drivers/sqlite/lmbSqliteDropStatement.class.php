<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__) . '/lmbSqliteStatement.class.php');

/**
 * class lmbSqliteDropStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteDropStatement extends lmbSqliteStatement
{
  function execute()
  {
    try
    {
      $this->queryId = @$this->connection->execute($this->getSQL());
      return (bool) $this->queryId;
    }
    catch(lmbException $e){}
  }
}


