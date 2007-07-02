<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/toolkit.inc.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/query/lmbUpdateQuery.class.php');
lmb_require('limb/dbal/src/query/lmbDeleteQuery.class.php');

/**
 * class lmbDBAL.
 *
 * @package dbal
 * @version $Id: lmbDBAL.class.php 6039 2007-07-02 12:51:09Z pachanga $
 */
class lmbDBAL
{
  static function setDefaultDSN($dsn)
  {
    lmbToolkit :: instance()->setDefaultDbDSN($dsn);
  }

  static function setEnvironment($env)
  {
    lmbToolkit :: instance()->setDbEnvironment($env);
  }

  static function newConnection($dsn)
  {
    return lmbToolkit :: instance()->createDbConnection($dsn);
  }

  static function newStatement($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    return $conn->newStatement($sql);
  }

  static function db($conn = null)
  {
    return new lmbSimpleDb($conn);
  }

  static function table($table, $conn = null)
  {
    return lmbToolkit :: instance()->createTableGateway($table, $conn);
  }

  static function selectQuery($table, $conn = null)
  {
    return new lmbSelectQuery($table, $conn);
  }

  static function updateQuery($table, $conn = null)
  {
    return new lmbUpdateQuery($table, $conn);
  }

  static function deleteQuery($table, $conn = null)
  {
    return new lmbDeleteQuery($table, $conn);
  }

  static function fetch($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $stmt = $conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  static function execute($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $conn->execute($sql);
  }
}

?>