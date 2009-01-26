<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbUriNormalizerDecorator.
 *
 * @package web_spider
 * @version $Id: lmbUriNormalizerDecorator.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbUriNormalizerDecorator
{
  var $decorated;

  function lmbUriNormalizerDecorator(&$decorated)
  {
    $this->decorated =& $decorated;
  }

  function reset()
  {
    $this->decorated->reset();
  }

  function stripAnchor($status = true)
  {
    $this->decorated->stripAnchor($status);
  }

  function stripQueryItem($key)
  {
    $this->decorated->stripQueryItem($key);
  }

  function process($uri)
  {
    $this->decorated->process($uri);
  }
}


