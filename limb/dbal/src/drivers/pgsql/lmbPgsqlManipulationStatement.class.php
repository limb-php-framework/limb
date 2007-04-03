<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlManipulationStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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