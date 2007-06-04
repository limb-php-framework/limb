<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheRequestRule.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class lmbFullPageCacheRequestRule
{
  protected $request;
  protected $get;
  protected $post;

  function __construct($request = null, $get = null, $post = null)
  {
    $this->request = $request;
    $this->get = $get;
    $this->post = $post;
  }

  function isSatisfiedBy($request)
  {
    $http_request = $request->getHttpRequest();

    if(!$this->_matches($this->request, $http_request->getRequest()))
      return false;

    if(!$this->_matches($this->get, $http_request->getGet()))
      return false;

    if(!$this->_matches($this->post, $http_request->getPost()))
      return false;

    return true;
  }

  function _matches($expected, $variable)
  {
    if(is_array($expected))
    {
      foreach($expected as $key => $value)
      {
        if(!isset($variable[$key]) || ($value != '*' && $value != $variable[$key]))
          return false;
      }
    }
    elseif($expected == '!' && !empty($variable))
      return false;
    elseif($expected == '*' && empty($variable))
      return false;

    return true;
  }
}

?>
