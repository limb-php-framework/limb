<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/lmbWebServerCookie.class.php');
lmb_require(dirname(__FILE__).'/lmbWebAgentCookieIterator.class.php');

/**
 * Web server cookies collection
 *
 * @package web_agent
 * @version $Id: lmbWebServerCookiesCollection.class.php 43 2007-10-05 15:33:11Z CatMan $
 */
class lmbWebServerCookiesCollection implements IteratorAggregate {

  protected $cookies = array();

  function add(lmbWebServerCookie $cookie)
  {
    $num = $this->search($cookie->name, $cookie->path, $cookie->domain);
    if($num === false)
      $this->cookies[] = $cookie;
    else
      $this->cookies[$num] = $cookie;
  }

  function search($name, $path = '', $domain = '')
  {
    foreach($this->cookies as $n => $cookie)
    {
      if($cookie->name == $name && $cookie->path == $path && $cookie->domain == $domain)
        return $n;
    }
    return false;
  }

  function get($num)
  {
    return isset($this->cookies[$num]) ? $this->cookies[$num] : false;
  }

  function getIterator()
  {
    return new lmbWebAgentCookieIterator($this->cookies);
  }

  function copyTo(lmbWebServerCookiesCollection $dest)
  {
    foreach($this->cookies as $cookie)
    {
    	$new_cookie = clone $cookie;
      $dest->add($new_cookie);
    }
  }

}
