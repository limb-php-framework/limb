<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlStatement.class.php');

/**
 * class lmbPgsqlManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlManipulationStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbPgsqlManipulationStatement extends lmbPgsqlStatement implements lmbDbManipulationStatement
{
  protected $queryId;

  function getAffectedRowCount()
  {
    if(is_resource($this->queryId))
      return pg_affected_rows($this->queryId);
  }

  function execute()
  {
    $this->queryId = parent :: execute();
    return $this->queryId;
  }
}


