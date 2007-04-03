<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciDumpLoader.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/dump/lmbSQLDumpLoader.class.php');

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