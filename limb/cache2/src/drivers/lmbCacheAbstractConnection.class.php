<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/lmbNonTransparentCache.interface.php');

/**
 * interface lmbCacheAbstractConnection.
 *
 * @package cache
 * @version $Id$
 */
abstract class lmbCacheAbstractConnection implements lmbNonTransparentCache
{
  protected $dsn;
  protected $prefix;

  function __construct(lmbUri $dsn)
  {
    $this->dsn = $dsn;

    foreach($dsn as $option_name => $option_value)
    {
      if(!is_null($option_value))
        $this->$option_name = $option_value;
    }

    foreach($dsn->getQueryItems() as $option_name => $option_value)
    {
      if(!is_null($option_value))
        $this->$option_name = $option_value;
    }
  }

  protected function _resolveKey($keys)
  {
    if(!$this->prefix)
      return $keys;

    if(is_array($keys))
    {
      $new_keys = array();
      foreach($keys as $pos => $key)
        $new_keys[$pos] = $this->prefix.$key;
    }
    else
    {
      $new_keys  = $this->prefix.'_'.$keys;
    }

    return $new_keys;
  }

  function get($keys)
  {
    $keys = $this->_resolveKey($keys);

    if(!is_array($keys))
    {
      $values = $this->_getSingleKeyValue($keys);
    }
    else
    {
      $values = array();
      foreach($keys as $key)
      {
        if(!is_null($value = $this->_getSingleKeyValue($key)))
          $values[$key] = $value;
      }
    }

    return $values;
  }

  abstract function getType();
}
