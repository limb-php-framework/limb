<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInnerUriNormalizerObserver.class.php 5014 2007-02-08 15:38:18Z pachanga $
 * @package    web_spider
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
