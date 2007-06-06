<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/dump/lmbSQLDumpLoader.class.php');

/**
 * class lmbOciDumpLoader.
 *
 * @package dbal
 * @version $Id: lmbOciDumpLoader.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbOciDumpLoader extends lmbSQLDumpLoader
{
  protected function _retrieveStatements($raw_sql)
  {
    $stmts = preg_split('~\n/\s*\n~', $raw_sql);
    $processed = array();
    foreach($stmts as $stmt)
    {
      if($stmt = $this->_processStatement($stmt))
        $processed[] = $stmt;
    }
    return $processed;
  }

  protected function _processStatement($sql)
  {
    if(!$sql = trim($sql))
      return null;

    if(strpos($sql, '/') == (strlen($sql) - 1))
      $sql = substr($sql, 0, strlen($sql) - 1);

    if(strpos($sql, ';') == (strlen($sql) - 1))
      return substr($sql, 0, strlen($sql) - 1);
    else
      return $sql;
  }
}
?>