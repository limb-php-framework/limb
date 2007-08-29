<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbUriNormalizer.
 *
 * @package web_spider
 * @version $Id: lmbUriNormalizer.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbUriNormalizer
{
  protected $strip_anchor;
  protected $stripped_query_items;

  function __construct()
  {
    $this->reset();
  }

  function reset()
  {
    $this->strip_anchor = true;
    $this->stripped_query_items = array();
  }

  function stripAnchor($status = true)
  {
    $this->strip_anchor = $status;
  }

  function stripQueryItem($key)
  {
    $this->stripped_query_items[] = $key;
  }

  function process($uri)
  {
    if($this->strip_anchor)
      $uri->setAnchor('');

    foreach($this->stripped_query_items as $key)
      $uri->removeQueryItem($key);
  }
}


