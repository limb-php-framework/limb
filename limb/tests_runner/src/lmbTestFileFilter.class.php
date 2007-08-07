<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbTestFileFilter.
 *
 * @package tests_runner
 * @version $Id: lmbTestFileFilter.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class lmbTestFileFilter
{
  protected $regex;

  function __construct($filters)
  {
    $this->regex = $this->_createRegex($filters);
  }

  function match($item)
  {
    return preg_match($this->regex, basename($item));
  }

  protected function _createRegex($filters)
  {
    $regex = implode('|', $filters);
    $regex = preg_quote($regex);
    $regex = str_replace(array('\*', '\|'), array('.*', '|'), $regex);
    return '~^(?:' . $regex. ')$~';
  }
}


