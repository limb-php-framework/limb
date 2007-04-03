<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUriNormalizer.class.php 5014 2007-02-08 15:38:18Z pachanga $
 * @package    web_spider
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

?>
