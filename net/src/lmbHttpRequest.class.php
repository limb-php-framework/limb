<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
 * @version $Id: lmbHttpRequest.class.php 8122 2010-02-02 09:54:14Z hidrarg $
 */
class lmbHttpRequest extends lmbSet
{
  protected $__uri;
  protected $__request = array();
  protected $__get = array();
  protected $__post = array();
  protected $__cookies = array();
  protected $__files = array();
  protected $__pretend_post = false;
  protected $__reserved_attrs = array('__uri', '__request', '__get', '__post', '__cookies', '__files', '__pretend_post', '__reserved_attrs');

  function __construct($uri_string = null, $get = null, $post = null, $cookies = null, $files = null)
  {
    parent :: __construct();
    $this->_initRequestProperties($uri_string, $get, $post, $cookies, $files);
  }

  protected function _initRequestProperties($uri_string, $get, $post, $cookies, $files)
  {
    $this->__uri = !is_null($uri_string) ? new lmbUri($uri_string) : new lmbUri($this->getRawUriString());

    $this->__get = !is_null($get) ? $get : $_GET;
    $items = $this->__uri->getQueryItems();
    foreach($items as $k => $v)
      $this->__get[$k] = $v;

    $this->__post = !is_null($post) ? $post : $_POST;
    $this->__cookies = !is_null($cookies) ? $cookies : $_COOKIE;
    $this->__files = !is_null($files) ? $this->_parseUploadedFiles($files) : $this->_parseUploadedFiles($_FILES);

    if(ini_get('magic_quotes_gpc'))
    {
      $this->__get = $this->_stripHttpSlashes($this->__get);
      $this->__post = $this->_stripHttpSlashes($this->__post);
      $this->__cookies = $this->_stripHttpSlashes($this->__cookies);
    }

    $this->__request = lmbArrayHelper :: arrayMerge($this->__get, $this->__post, $this->__files);

    foreach($this->__request as $k => $v)
    {
      if(in_array($k, $this->__reserved_attrs))
        continue;
      $this->set($k, $v);
    }
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

  function hasFiles($key = null)
  {
    return $this->_get($this->__files, $key);
  }

  function getFiles($key = null)
  {
    return $this->_get($this->__files, $key);
  }

  function getFile($name)
  {
    $file = $this->getFiles($name);
    if(is_object($file))
      return $file;
  }

  function getRequest($key = null, $default = LIMB_UNDEFINED)
  {
    return $this->_get($this->__request, $key, $default);
  }

  function getGet($key = null, $default = LIMB_UNDEFINED)
  {
    return $this->_get($this->__get, $key, $default);
  }

  function getPost($key = null, $default = LIMB_UNDEFINED)
  {
    return $this->_get($this->__post, $key, $default);
  }

  function hasPost()
  {
    if($this->__pretend_post)
      return true;

    return sizeof($this->__post) > 0 ||
      (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST');
  }

  function pretendPost($flag = true)
  {
    $this->__pretend_post = $flag;
  }

  function getCookie($key = null, $default = LIMB_UNDEFINED)
  {
    return $this->_get($this->__cookies, $key, $default);
  }

  function getSafe($var,$default = LIMB_UNDEFINED)
  {
    return htmlspecialchars(parent :: get($var,$default));
  }

  function getFiltered($key, $filter, $default = LIMB_UNDEFINED)
  {
    return filter_var($this->get($key, $default), $filter);
  }

  function getGetFiltered($key, $filter, $default = LIMB_UNDEFINED)
  {
    $value = $this->getGet($key, $default);
    if (is_array($key))
      return filter_var_array($value, $filter);
    else
      return filter_var($value, $filter);
  }

  function getPostFiltered($key, $filter, $default = LIMB_UNDEFINED)
  {
    $value = $this->getPost($key, $default);
    if (is_array($key))
      return filter_var_array($value, $filter);
    else
      return filter_var($value, $filter);
  }

  protected function _get(&$arr, $key = null, $default = LIMB_UNDEFINED)
  {
    if(is_null($key))
      return $arr;

    if(is_array($key))
    {
      $ret = array();
      foreach($key as $item)
        $ret[$item] = (isset($arr[$item]) ? $arr[$item] : null);
      return $ret;
    }
    elseif(isset($arr[$key]))
      return $arr[$key];
    elseif($default !== LIMB_UNDEFINED)
      return $default;
  }

  function get($key, $default = LIMB_UNDEFINED)
  {
    $_key = "__$key";
    if(in_array($_key, $this->__reserved_attrs))
      return $this->$_key;

    return parent::get($key, $default);
  }

  function getUri()
  {
    return $this->__uri;
  }

  function getUriPath()
  {
    return $this->__uri->getPath();
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

    lmbArrayHelper :: toFlatArray($this->__request, $flat);

    foreach($flat as $key => $value)
    {
      if(is_object($value))
        continue;
      $query .= $key . '=' . urlencode($value) . '&';
    }

    //TODO: this is quite ugly but it works...
    $uri = clone($this->__uri);
    $uri->removeQueryItems();
    return rtrim($uri->toString() . '?' . rtrim($query, '&'), '?');
  }

  function dump()
  {
    return $this->toString();
  }
}


