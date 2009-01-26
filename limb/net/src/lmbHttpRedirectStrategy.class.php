<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbHttpRedirectStrategy.
 *
 * @package net
 * @version $Id: lmbHttpRedirectStrategy.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbHttpRedirectStrategy
{
  function redirect($response, $path)
  {
    $response->addHeader("Location: {$path}");
  }
}


