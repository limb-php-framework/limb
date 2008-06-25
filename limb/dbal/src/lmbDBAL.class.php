<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/query/lmbUpdateQuery.class.php');
lmb_require('limb/dbal/src/query/lmbDeleteQuery.class.php');

/**
 * class lmbDBAL.
 *
 * @package dbal
 * @version $Id: lmbDBAL.class.php 7065 2008-06-25 12:00:59Z korchasa $
 */
class lmbDBAL
{
  /**
   * @param lmbDbDSN $dsn
   */
  static function setDefaultDSN($dsn)
  {
    lmbToolkit :: instance()->setDefaultDbDSN($dsn);
  }

  static function setEnvironment($env)
  {
    lmbToolkit :: instance()->setDbEnvironment($env);
  }

  /**
   * @param lmbDbDSN $dsn
   * @return lmbDbConnetion
   */
  static function newConnection($dsn)
  {
    return lmbToolkit :: instance()->createDbConnection($dsn);
  }

  /**
   * @param lmbDbDSN $dsn
   * @return lmbDbConnetion
   */
  static function defaultConnection()
  {
    return lmbToolkit :: instance()->getDefaultDbConnection();
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   * @return lmbDbStatement
   */
  static function newStatement($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    return $conn->newStatement($sql);
  }

  /**
   * @param [lmbDbConnetion] $conn
   * @return lmbSimpleDb
   */
  static function db($conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();

    return new lmbSimpleDb($conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbTableGateway
   */
  static function table($table, $conn = null)
  {
    return lmbToolkit :: instance()->createTableGateway($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbSelectQuery
   */
  static function selectQuery($table, $conn = null)
  {
    return new lmbSelectQuery($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbUpdateQuery
   */
  static function updateQuery($table, $conn = null)
  {
    return new lmbUpdateQuery($table, $conn);
  }

  /**
   * @param string $table
   * @param [lmbDbConnection] $conn
   * @return lmbDeleteQuery
   */
  static function deleteQuery($table, $conn = null)
  {
    return new lmbDeleteQuery($table, $conn);
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   * @return lmbDbRecordSet
   */
  static function fetch($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $stmt = $conn->newStatement($sql);
    return $stmt->getRecordSet();
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   * @return string
   */
  static function fetchOneValue($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    return $conn->newStatement($sql)->getOneValue();
  }

  /**
   * @param string $sql
   * @param [lmbDbConnection] $conn
   */
  static function execute($sql, $conn = null)
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $conn->execute($sql);
  }
}


