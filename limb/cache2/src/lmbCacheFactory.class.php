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
 * class lmbCacheFactory
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
      $connection = self::applyWrapper($connection, $wrapper);

    return $connection;
  }

  /**
   * @param lmbUri $dsn
   * @return array
   */
  static protected function getWrappers($dsn)
  {
    if(!$wrappers = $dsn->getQueryItem('wrapper'))
      return array();

    if(!is_array($wrappers))
      $wrappers = array($wrappers);

    return $wrappers;
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

  static protected function applyWrapper($connection, $wrapper_name)
  {
    $wrapper_class = 'lmb'. ucfirst($wrapper_name) . 'CacheWrapper';
    if(!class_exists($wrapper_class))
    {
      $file = DIRNAME(__FILE__).'/wrappers/' . $wrapper_class . '.class.php';
      if(!file_exists($file))
        throw new lmbException(
          "Cache wripper '$wrapper_class' file not found",
          array(
            'dsn'   => $dsn,
            'name'  => $wrapper_name,
            'class' => $wrapper_class,
            'file'  => $file,
        )
      );

      lmb_require($file);
    }
  	return new $wrapper_class($connection);
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