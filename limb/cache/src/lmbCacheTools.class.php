<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

class lmbCacheTools extends lmbAbstractTools
{
  protected $cache;

  function getCache()
  {
    if(is_object($this->cache))
      return $this->cache;

    lmb_require('limb/cache/src/lmbCachePersisterKeyDecorator.class.php');
    $this->cache = new lmbCachePersisterKeyDecorator(new lmbCacheMemoryPersister());

    return $this->cache;
  }

  function setCache($cache)
  {
    $this->cache = $cache;
  }
}
?>
