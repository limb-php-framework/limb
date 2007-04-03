<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedPagedIterator.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
 */
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');
lmb_require('limb/datasource/src/lmbPagedDatasetDecorator.class.php');

define('LIMB_RS_CACHE_COMMON_GROUP', 'rs');
define('LIMB_RS_TOTAL_CACHE_COMMON_GROUP', 'rs_total');

//Note that the code is alpha, we rely on PHP serialize skip resource type variables
//feature. It would be much more efficient if $iterators implemented getHash() somehow

class lmbCachedPagedIterator extends lmbPagedDatasetDecorator
{
  protected $cached_rs;
  protected $cached_total_row_count;
  protected $is_cached_rs = false;
  protected $is_cached_total = false;
  protected $cache;
  protected $cache_key_for_rs;
  protected $cache_key_for_total;

  function __construct($iterator, $cache)
  {
    parent :: __construct($iterator);

    $this->cache = $cache;
    $this->cache_key_for_rs = $iterator;
    $this->cache_key_for_total = $iterator;
  }

  function paginate($offset, $limit)
  {
    parent :: paginate($offset, $limit);
    $this->cache_key_for_rs = $this->iterator;
  }

  function rewind()
  {
    $this->_checkRsCache();

    if($this->is_cached_rs)
    {
      $this->cached_rs->rewind();
      return;
    }

    $clean_iterator = $this->iterator;
    $tmp_cache = array();

    foreach($this->iterator as $record)
      $tmp_cache[] = $record->export();

    $this->is_cached_rs = true;
    $this->cached_rs = new lmbPagedArrayDataset($tmp_cache);
    $clean_cached_rs = $this->cached_rs;

    $this->cache->put($clean_iterator, $clean_cached_rs, LIMB_RS_CACHE_COMMON_GROUP);

    $this->cached_rs->rewind();
  }

  function next()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->next();
    else
      return parent :: next();
  }

  function valid()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->valid();
    else
      return parent :: valid();
  }

  function current()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->current();
    else
      return parent :: current();
  }

  function key()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->key();
    else
      return parent :: key();
  }

  function countPaginated()
  {
    if($this->is_cached_rs)
      return $this->cached_rs->countPaginated();
    else
      return parent :: countPaginated();
  }

  function count()
  {
    $this->_checkRsCacheTotal();

    if($this->is_cached_total)
      return $this->cached_total_row_count;

    $this->cached_total_row_count = parent :: count();

    $this->cache->put($this->cache_key_for_rs,
                      $this->cached_total_row_count,
                      LIMB_RS_TOTAL_CACHE_COMMON_GROUP);

    return $this->cached_total_row_count;
  }

  protected function _checkRsCache()
  {
    if(($cached_rs = $this->cache->get($this->cache_key_for_rs, LIMB_RS_CACHE_COMMON_GROUP)) !== LIMB_CACHE_NULL_RESULT)
    {
      $this->cached_rs = $cached_rs;
      $this->is_cached_rs = true;
    }
    else
      $this->is_cached_rs = false;
  }

  protected function _checkRsCacheTotal()
  {
    if(($count = $this->cache->get($this->cache_key_for_total, LIMB_RS_TOTAL_CACHE_COMMON_GROUP)) !== LIMB_CACHE_NULL_RESULT)
    {
      $this->cached_total_row_count = $count;
      $this->is_cached_total = true;
    }
    else
      $this->is_cached_total = false;
  }
}

?>
