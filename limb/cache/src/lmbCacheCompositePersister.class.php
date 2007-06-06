<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cache/src/lmbCachePersister.interface.php');

/**
 * class lmbCacheCompositePersister.
 *
 * @package cache
 * @version $Id: lmbCacheCompositePersister.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbCacheCompositePersister implements lmbCachePersister
{
  protected $persisters = array();

  function registerPersister($persister)
  {
    $this->persisters[] = $persister;
  }

  function getId()
  {
    return null;
  }

  function put($key, $value, $group = 'default')
  {
    foreach(array_keys($this->persisters) as $index)
      $this->persisters[$index]->put($key, $value, $group);
  }

  function get($key, $group = 'default')
  {
    foreach(array_keys($this->persisters) as $index)
    {
      if(($value = $this->persisters[$index]->get($key, $group)) !== LIMB_CACHE_NULL_RESULT)
      {
        $this->_putValueToPersisters($index, $value, $key, $group);
        return $value;
      }
    }
    return LIMB_CACHE_NULL_RESULT;
  }

  function flushValue($key, $group = 'default')
  {
    foreach(array_keys($this->persisters) as $index)
      $this->persisters[$index]->flushValue($key, $group);
  }

  function flushGroup($group)
  {
    foreach(array_keys($this->persisters) as $index)
      $this->persisters[$index]->flushGroup($group);
  }

  function flushAll()
  {
    foreach(array_keys($this->persisters) as $index)
      $this->persisters[$index]->flushAll();
  }

  protected function _putValueToPersisters($index, &$value, $key, $group)
  {
    for($i=0; $i < $index; $i++)
      $this->persisters[$i]->put($key, $value, $group);
  }
}
?>
