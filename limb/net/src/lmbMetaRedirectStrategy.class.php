<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMetaRedirectStrategy.
 *
 * @package net
 * @version $Id: lmbMetaRedirectStrategy.class.php 6243 2007-08-29 11:53:10Z pachanga $
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


