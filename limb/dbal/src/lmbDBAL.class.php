<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDBAL.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

class lmbDBAL
{
  static function setDefaultDSN($dsn)
  {
    require_once('limb/dbal/toolkit.inc.php');
    lmbToolkit :: instance()->setDefaultDbDSN($dsn);
  }

  static function newConnection($dsn)
  {
    $driver = $dsn->getDriver();
    $class = 'lmb' . ucfirst($driver) . 'Connection';

    lmb_require(dirname(__FILE__) . '/drivers/' . $driver . '/' . $class . '.class.php');
    return new $class($dsn);
  }
}

?>