<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Web agent cookies
 *
 * @package web_agent
 * @version $Id: lmbWebAgentCookieIterator.class.php 39 2007-10-03 21:08:36Z CatMan $
 */
class lmbWebAgentCookieIterator implements Iterator {

  protected $cookies;

  function __construct($cookies)
  {
    $this->cookies = $cookies;
  }

  function rewind()
  {
    reset($this->cookies);
  }

  function current()
  {
    return current($this->cookies);
  }

  function key()
  {
    return key($this->cookies);
  }

  function next()
  {
    return next($this->cookies);
  }

  function valid()
  {
    return $this->current() !== false;
  }

}
