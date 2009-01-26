<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
/**
 * class lmbCacheTools.
 *
 * @package cache
 * @version $Id: lmbCacheTools.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbCacheTools extends lmbAbstractTools
{
  protected $_cache;

  function getCache()
  {
    if(is_object($this->_cache))
      return $this->_cache;
    
    lmb_require('limb/cache/src/lmbCacheGroupDecorator.class.php');
    lmb_require('limb/cache/src/lmbCacheFileBackend.class.php');

    $this->_cache = new lmbCacheGroupDecorator(new lmbCacheFileBackend(LIMB_VAR_DIR . '/cache'));

    return $this->_cache;
  }

  function setCache($cache)
  {
    $this->_cache = $cache;
  }
}

