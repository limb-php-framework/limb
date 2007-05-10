<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDBAL.class.php 5851 2007-05-10 08:52:00Z pachanga $
 * @package    dbal
 */

class lmbDBAL
{
  static function setDefaultDSN($dsn)
  {
    lmb_require('limb/dbal/toolkit.inc.php');
    lmbToolkit :: instance()->setDefaultDbDSN($dsn);
  }

  static function newConnection($dsn)
  {
    $driver = $dsn->getDriver();
    $class = 'lmb' . ucfirst($driver) . 'Connection';
    $file = dirname(__FILE__) . '/drivers/' . $driver . '/' . $class . '.class.php';
    if(!file_exists($file))
      throw new lmbException("Driver '$driver' file not found for DSN '" . $dsn->toString() . "'!");

    lmb_require($file);
    return new $class($dsn);
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