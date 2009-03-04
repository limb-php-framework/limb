<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbInnerUriNormalizerObserver.
 *
 * @package web_spider
 * @version $Id: lmbInnerUriNormalizerObserver.class.php 7686 2009-03-04 19:57:12Z korchasa $
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


