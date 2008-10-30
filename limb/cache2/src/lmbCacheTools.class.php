<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/cache2/src/lmbCache.class.php');

/**
 * class lmbCacheTools.
 *
 * @package cache
 * @version $Id: lmbCacheTools.class.php 6588 2007-12-05 22:10:21Z alex433 $
 */
class lmbCacheTools extends lmbAbstractTools
{
  protected $_cache;

  function getCache()
  {
    if(is_object($this->_cache))
      return $this->_cache;
    
    $dsn = lmbToolkit::instance()->getConf('cache')->get('dsn');
    $this->_cache = lmbCache::createConnection($dsn);

    return $this->_cache;
  }

  function setCache($cache)
  {
    $this->_cache = $cache;
  }
}

