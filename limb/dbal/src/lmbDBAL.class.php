<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/toolkit.inc.php');

/**
 * class lmbDBAL.
 *
 * @package dbal
 * @version $Id: lmbDBAL.class.php 5945 2007-06-06 08:31:43Z pachanga $
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

  static function query($sql, $conn = null)
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