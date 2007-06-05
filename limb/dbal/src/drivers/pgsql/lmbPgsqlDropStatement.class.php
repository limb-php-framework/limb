<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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
