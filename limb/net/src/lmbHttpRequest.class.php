<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbSet.class.php');
lmb_require('limb/core/src/lmbArrayHelper.class.php');
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/net/src/lmbUploadedFilesParser.class.php');

/**
 * class lmbHttpRequest.
 *
 * @package net
 * @version $Id: lmbHttpRequest.class.php 6875 2008-03-31 12:08:36Z pachanga $
 */
class lmbHttpRequest extends lmbSet
{
  protected $uri;
  protected $request = array();
  protected $get = array();
  protected $post = array();
  protected $cookies = array();
  protected $files = array();
  protected $pretend_post = false;

  function __construct($uri_string = null, $get = null, $post = null, $cookies = null, $files = null)
  {
    parent :: __construct();
    $this->_initRequestProperties($uri_string, $get, $post, $cookies, $files);
  }

  protected function _initRequestProperties($uri_string, $get, $post, $cookies, $files)
  {
    $this->uri = !is_null($uri_string) ? new lmbUri($uri_string) : new lmbUri($this->getRawUriString());

    $this->get = !is_null($get) ? $get : $_GET;
    $items = $this->uri->getQueryItems();
    foreach($items as $k => $v)
      $this->get[$k] = $v;

    $this->post = !is_null($post) ? $post : $_POST;
    $this->cookies = !is_null($cookies) ? $cookies : $_COOKIE;
    $this->files = !is_null($files) ? $this->_parseUploadedFiles($files) : $this->_parseUploadedFiles($_FILES);

    if(ini_get('magic_quotes_gpc'))
    {
      $this->get = $this->_stripHttpSlashes($this->get);
      $this->post = $this->_stripHttpSlashes($this->post);
      $this->cookies = $this->_stripHttpSlashes($this->cookies);
    }

    $this->request = lmbArrayHelper :: arrayMerge($this->get, $this->post, $this->files);

    foreach($this->request as $k => $v)
      $this->set($k, $v);
  }

  protected function _parseUploadedFiles($files)
  {
    $parser = new lmbUploadedFilesParser();
    return $parser->objectify($files);
  }

  protected function _stripHttpSlashes($data, $result=array())
  {
    foreach($data as $k => $v)
    {
      if(is_array($v))
        $result[$k] = $this->_stripHttpSlashes($v);
      else
        $result[$k] = stripslashes($v);
    }
    return $result;
  }

  /**
   * @deprecated
   */
  function hasAttribute($name)
  {
    return $this->has($name);
  }

  function getFiles($key = null)
  {
    $this->_ensureMultipartFormData();

    return $this->_get('files', $key);
  }

  function getFile($name)
  {
    $file = $this->getFiles($name);
    if(is_object($file))
      return $file;
  }

  function getRequest($key = null)
  {
    return $this->_get('request', $key);
  }

  function getGet($key = null)
  {
    return $this->_get('get', $key);
  }

  function getPost($key = null)
  {
    return $this->_get('post', $key);
  }

  function hasPost()
  {
    if($this->pretend_post)
      return true;

    return sizeof($this->post) > 0 ||
      (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST');
  }

  function pretendPost($flag = true)
  {
    $this->pretend_post = $flag;
  }

  function getCookie($key = null)
  {
    return $this->_get('cookies', $key);
  }

  function getSafe($var)
  {
    return htmlspecialchars(parent :: get($var));
  }

  protected function _get($var, $key = null)
  {
    if(is_null($key))
      return $this->$var;

    $arr = $this->$var;
    if(isset($arr[$key]))
      return $arr[$key];
  }

  function getUri()
  {
    return $this->uri;
  }

  function getUriPath()
  {
    return $this->uri->getPath();
  }

  function getRawUriString()
  {
    $host = 'localhost';
    if(!empty($_SERVER['HTTP_HOST']))
    {
      $items = explode(':', $_SERVER['HTTP_HOST']);
      $host = $items[0];
      $port = isset($items[1]) ? $items[1] : null;
    }
    elseif(!empty($_SERVER['SERVER_NAME']))
    {
      $items = explode(':', $_SERVER['SERVER_NAME']);
      $host = $items[0];
      $port = isset($items[1]) ? $items[1] : null;
    }

    if(isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on'))
      $protocol = 'https';
    else
      $protocol = 'http';

    if(!isset($port) || $port != intval($port))
      $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;

    if($protocol == 'http' && $port == 80)
      $port = null;

    if($protocol == 'https' && $port == 443)
      $port = null;

    $server = $protocol . '://' . $host . (isset($port) ? ':' . $port : '');

    if(isset($_SERVER['REQUEST_URI']))
      $url = $_SERVER['REQUEST_URI'];
    elseif(isset($_SERVER['QUERY_STRING']))
      $url = basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING'];
    else
      $url = $_SERVER['PHP_SELF'];

    return $server . $url;
  }

  function toString()
  {
    $flat = array();
    $query = '';

    lmbArrayHelper :: toFlatArray($this->request, $flat);

    foreach($flat as $key => $value)
    {
      if(is_object($value))
        continue;
      $query .= $key . '=' . $value . '&';
    }

    $uri = clone($this->uri);
    $uri->removeQueryItems();
    return rtrim($uri->toString() . '?' . rtrim($query, '&'), '?');
  }

  function dump()
  {
    return $this->toString();
  }

  protected function _ensureMultipartFormData()
  {
    if(!$this->hasPost() || $this->files)
      return;

    if(strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') === false)
      throw new lmbException("Submitted form does not have enctype='multipart/form-data' attribute, no files loaded!");
  }
}


