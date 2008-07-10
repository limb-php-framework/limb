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
 * @version $Id: lmbHttpRedirectStrategy.class.php 7111 2008-07-10 09:34:17Z korchasa $
 */
class lmbHttpRedirectStrategy
{
  function redirect($response, $path)
  {
    $response->addHeader("Location: {$path}");
  }
}


