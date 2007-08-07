<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__) . '/lmbPgsqlStatement.class.php');

/**
 * class lmbPgsqlDropStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlDropStatement.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class lmbPgsqlDropStatement extends lmbPgsqlStatement
{
  function execute()
  {
    try
    {
      $this->queryId = @$this->connection->execute($this->getSQL());
      return (bool) $this->queryId;
    }
    catch(lmbException $e)
    {
    }
  }
}


