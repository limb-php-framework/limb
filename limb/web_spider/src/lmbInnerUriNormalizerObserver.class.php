<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbInnerUriNormalizerObserver.
 *
 * @package web_spider
 * @version $Id: lmbInnerUriNormalizerObserver.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbInnerUriNormalizerObserver
{
  protected $base_uri = null;

  function __construct($base_uri)
  {
    $this->base_uri = $base_uri;
  }

  function notify($reader)
  {
    $uri = $reader->getUri();
    if($uri->getHost() != $this->base_uri->getHost())
      return;
    if($uri->getPort() != $this->base_uri->getPort())
      return;
    if($uri->getProtocol() != $this->base_uri->getProtocol())
      return;

    $uri->setHost('');
    $uri->setPort('');
    $uri->setProtocol('');
  }
}

?>
