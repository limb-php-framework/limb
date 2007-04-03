<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheUriPathRule.class.php 5013 2007-02-08 15:38:13Z pachanga $
 * @package    web_cache
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

?>
