<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbFullPageCacheUriPathRule.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheUriPathRule.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbFullPageCacheUriPathRule
{
  protected $path_regex;
  protected $offset;
  protected $dont_negate = true;

  function __construct($path_regex, $dont_negate = true)
  {
    $this->path_regex = $path_regex;
    $this->dont_negate = $dont_negate;
  }

  function useOffset($offset)
  {
    $this->offset = $offset;
  }

  function isSatisfiedBy($request)
  {
    $path = $request->getHttpRequest()->getUriPath();
    $path = $this->_applyOffset($path);

    if($this->dont_negate)
      return preg_match($this->path_regex, $path);
    else
      return !preg_match($this->path_regex, $path);
  }

  function _applyOffset($path)
  {
    if(!$this->offset)
      return $path;

    $pieces = explode($this->offset, $path, 2);
    return isset($pieces[1]) ? $pieces[1] : $pieces[0];
  }
}


