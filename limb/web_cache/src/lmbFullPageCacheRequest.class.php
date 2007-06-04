<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheRequest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/core/src/lmbArrayHelper.class.php');

class lmbFullPageCacheRequest
{
  protected $http_request;
  protected $user;
  protected $ignore_user_groups = array();

  function __construct($http_request, $user)
  {
    $this->http_request = $http_request;
    $this->user = $user;
  }

  function getUri()
  {
    return $this->http_request->getUri();
  }

  function getHttpRequest()
  {
    return $this->http_request;
  }

  function getUser()
  {
    return $this->user;
  }

  function getHash()
  {
    $path = $this->http_request->getUriPath();

    $extra = $this->_serializeHttpAttributes();
    $extra .= $this->_serializeUserInfo();

    return $path . ($extra ? '_' . md5($extra) : '') . '/';
  }

  protected function _serializeHttpAttributes()
  {
    $uri = $this->http_request->getUri();

    if(!$query = $uri->getQueryItems())
      return '';

    $flat = array();
    lmbArrayHelper :: toFlatArray($query, $flat);
    ksort($flat);
    return serialize($flat);
  }

  protected function _serializeUserInfo()
  {
    $groups = $this->user->getGroups();

    if(!$groups || array_values($groups) == $this->ignore_user_groups)
      return '';

    sort($groups);
    return serialize($groups);
  }
}

?>
