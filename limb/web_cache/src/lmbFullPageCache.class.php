<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbFullPageCache.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCache.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbFullPageCache
{
  protected $policy;
  protected $cache_writer;
  protected $session_opened = false;
  protected $cache_request;

  function __construct($cache_writer, $policy)
  {
    $this->cache_writer = $cache_writer;
    $this->policy = $policy;
  }

  protected function _isValidSession()
  {
    return ($this->session_opened && is_object($this->cache_request));
  }

  function get()
  {
    if(!$this->_isValidSession())
      return false;

    return $this->cache_writer->get($this->_getHash());
  }

  function save($content)
  {
    if(!$this->_isValidSession())
      return false;

    return $this->cache_writer->save($this->_getHash(), $content);
  }

  protected function _getHash()
  {
    return $this->cache_request->getHash();
  }

  function openSession($cache_request)
  {
    $this->session_opened = false;

    if(!$ruleset = $this->policy->findRuleset($cache_request))
      return false;

    if($ruleset->isDeny())
      return false;

    $this->session_opened = true;
    $this->cache_request = $cache_request;

    return true;
  }

  function flush($hash = null)
  {
    if(!$hash)
      $this->cache_writer->flushAll();
    else
      $this->cache_writer->flush($hash);
  }
}


