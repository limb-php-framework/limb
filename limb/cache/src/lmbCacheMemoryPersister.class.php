<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCacheMemoryPersister.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
 */
lmb_require('limb/cache/src/lmbCachePersister.interface.php');

class lmbCacheMemoryPersister implements lmbCachePersister
{
  protected $cache = array();
  protected $id;

  function __construct($id = 'cache')
  {
    $this->id = $id;
  }

  function getId()
  {
    return $this->id;
  }

  function put($key, $value, $group = 'default')
  {
    $this->cache[$group][$key] = $value;
  }

  function get($key, $group = 'default')
  {
    if(isset($this->cache[$group]) &&
       array_key_exists($key, $this->cache[$group]))
    {
      return $this->cache[$group][$key];
    }

    return LIMB_CACHE_NULL_RESULT;
  }

  function flushValue($key, $group = 'default')
  {
    if(isset($this->cache[$group][$key]))
      unset($this->cache[$group][$key]);
  }

  function flushGroup($group)
  {
    if(isset($this->cache[$group]))
      $this->cache[$group] = array();
  }

  function flushAll()
  {
    $this->cache = array();
  }
}
?>
