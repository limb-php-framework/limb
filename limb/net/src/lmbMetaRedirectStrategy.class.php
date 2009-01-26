<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMetaRedirectStrategy.
 *
 * @package net
 * @version $Id: lmbMetaRedirectStrategy.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbMetaRedirectStrategy
{
  protected $template_path;

  function __construct($template_path = null)
  {
    $this->template_path = $template_path;
  }

  function redirect($response, $path)
  {
    $response->write($this->_prepareDefaultResponse('Redirecting...', $path));
  }

  protected function _prepareDefaultResponse($message, $path)
  {
    return "<html><head><meta http-equiv=refresh content='0;url={$path}'></head>
            <body bgcolor=white>{$message}</body></html>";
  }

}


