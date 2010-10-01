<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2010 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/net/src/lmbUri.class.php');

/**
 * class lmbInputStreamParser.
 *
 * @package net
 * @version $Id$
 */
class lmbInputStreamParser
{
  function parse()
  {
    $put = fopen("php://input", "r");

    $query_string = '';
    while($put_string = fread($put, 1024))
      $query_string .= $put_string;

    $uri = new lmbUri();
    $uri->setQueryString($query_string);

    return $uri->getQueryItems();
  }
}