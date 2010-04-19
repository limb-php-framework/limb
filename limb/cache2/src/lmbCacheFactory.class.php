<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/net/src/lmbUri.class.php');

/**
 * class lmbCache.
 *
 * @package cache
 * @version $Id: lmbDBAL.class.php 6930 2008-04-14 11:22:49Z pachanga $
 */
class lmbCacheFactory
{
  /**
   * @param string $dsn
   * @return lmbCacheAbstractConnection
   */
  static function createConnection($dsn)
  {
    if(!is_object($dsn))
      $dsn = new lmbUri($dsn);

    $class = self::getConnectionClass($dsn);
    $connection = new $class($dsn);

    foreach(self::getWrappers($dsn) as $wrapper)
      $connection = new $wrapper($connection);

    return $connection;
  }

  static protected function getConnectionClass($dsn)
  {
    $driver = $dsn->getProtocol();

    $class = 'lmbCache' . ucfirst($driver) . 'Connection';

    if(!class_exists($class))
    {
      $file = DIRNAME(__FILE__).'/drivers/' . $class . '.class.php';
      if(!file_exists($file))
        throw new lmbException("Cache driver '$driver' file not found for DSN '" . $dsn->toString() . "'!");

      lmb_require($file);
    }

    return $class;
  }

  static protected function getWrappers($dsn)
  {
    $wrapper = $dsn->getQueryItem('wrapper');
    return $wrapper ? (array) $wrapper : array();
  }

  /**
   * @param string $dsn
   * @return lmbLoggedCache
   */
  static function createLoggedConnection($dsn, $name)
  {
    return new lmbLoggedCache(self::createConnection($dsn), $name);
  }
}