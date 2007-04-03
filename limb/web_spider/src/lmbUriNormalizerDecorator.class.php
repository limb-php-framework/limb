<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUriNormalizerDecorator.class.php 5014 2007-02-08 15:38:18Z pachanga $
 * @package    web_spider
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

?>
