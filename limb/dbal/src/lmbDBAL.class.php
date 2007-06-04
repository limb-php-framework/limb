<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDBAL.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/toolkit.inc.php');

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