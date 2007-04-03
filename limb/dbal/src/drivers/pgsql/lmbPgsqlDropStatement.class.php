<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlDropStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

lmb_require(dirname(__FILE__) . '/lmbPgsqlStatement.class.php');

class lmbPgsqlDropStatement extends lmbPgsqlStatement
{
  function execute()
  {
    try
    {
      $this->queryId = @$this->connection->execute($this->getSQL());
      return (Boolean) $this->queryId;
    }
    catch(lmbException $e)
    {
    }
  }
}

?>
