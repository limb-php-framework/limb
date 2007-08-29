<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbHttpRedirectStrategy.
 *
 * @package net
 * @version $Id: lmbHttpRedirectStrategy.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbHttpRedirectStrategy
{
  function redirect($response, $path)
  {
    $response->header("Location: {$path}");
  }
}


