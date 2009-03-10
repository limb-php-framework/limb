<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cache/src/lmbCacheBackend.interface.php');

/**
 * class lmbCacheMemoryBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheMemoryBackend implements lmbCacheBackend
{
  protected $_cache = array();

  function add($key, $value, $params = array())
  {
    if (array_key_exists($key, $this->_cache))
      return false;
      
    $this->_cache[$key] = $value;
    return true;
  }
  
  function set($key, $value, $params = array())
  {
    $this->_cache[$key] = $value;
    return true;
  }

  function get($key, $params = array())
  {
    if(!isset($this->_cache[$key]))
      return false;
    
    return $this->_cache[$key];
  }

  function delete($key, $params = array())
  {
    unset($this->_cache[$key]);
  }

  function flush()
  {
    $this->_cache = array();
  }
  
  function stat($params = array())
  {
    return array();
  }
}

