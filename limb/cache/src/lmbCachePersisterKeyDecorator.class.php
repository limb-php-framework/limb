<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cache/src/lmbCachePersister.interface.php');

class lmbCachePersisterKeyDecorator implements lmbCachePersister
{
  protected $persister;

  function __construct($persister)
  {
    $this->persister = $persister;
  }

  function getId()
  {
    return $this->persister->getId();
  }

  function put($raw_key, $value, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);
    $this->persister->put($key, $value, $group);
  }

  function get($raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);
    return $this->persister->get($key, $group);
  }

  function flushValue($raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);
    $this->persister->flushValue($key, $group);
  }

  function flushGroup($group)
  {
    $this->persister->flushGroup($group);
  }

  function flushAll()
  {
    $this->persister->flushAll();
  }

  protected function _normalizeKey($key)
  {
    if(is_scalar($key))
      return $key;
    else
      return md5(serialize($key));
  }
}
?>
