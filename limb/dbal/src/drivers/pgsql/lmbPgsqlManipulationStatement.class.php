<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlManipulationStatement.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlStatement.class.php');

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

?>