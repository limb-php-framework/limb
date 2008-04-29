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

/**
 * class lmbUri.
 *
 * @package net
 * @version $Id: lmbUri.class.php 6974 2008-04-29 11:20:46Z korchasa $
 */
class lmbUri extends lmbSet
{
  protected $protocol = '';
  protected $user = '';
  protected $password = '';
  protected $host = '';
  protected $port = '';
  protected $path = '';
  protected $anchor = '';
  protected $query_items = array();
  protected $path_elements = array();

  function __construct($str='')
  {
    if($str)
      $this->parse($str);
  }

  static function addQueryItems($url, $items=array())
  {
    $str_params = '';

    if(strpos($url, '?') === false)
      $url .= '?';
    else
      $url .= '&';

    $str_params_arr = array();
    foreach($items as $key => $val)
    {
      $url = preg_replace("/&*{$key}=[^&]*/", '', $url);
      $str_params_arr[] = "$key=$val";
    }

    $items = explode('#', $url);

    $url = $items[0];
    $fragment = isset($items[1]) ? '#' . $items[1] : '';

    return $url . implode('&', $str_params_arr) . $fragment;
  }

  /**
   * @deprecated
   */
  function parse($uri)
  {
    if('file' == substr($uri, 0, 4))
      $uri = $this->_fixFileProtocol($uri);

    $this->reset($uri);
  }

  protected function _fixFileProtocol($url)
  {
    $matches = array();
    if(preg_match('/^file:\/\/([a-zA-Z]?:[\/].*)/', $url, $matches))
      $url = 'file:///' . $matches[1];
    return $url;
  }

  function reset($str = null)
  {
    $this->user        = '';
    $this->password    = '';
    $this->host        = '';
    $this->port        = '';
    $this->path        = '';
    $this->query_items = array();
    $this->anchor      = '';
    $this->path_elements = array();

    if(!$str)
      return;

    if(!$urlinfo = @parse_url($str))
      throw new lmbException("URI '$str' is not valid");

    foreach($urlinfo as $key => $value)
    {
      switch($key)
      {
        case 'scheme':
          $this->setProtocol($value);
        break;

        case 'user':
          $this->setUser($value);
        break;

        case 'host':
          $this->setHost($value);
        break;

        case 'port':
          $this->setPort($value);
        break;

        case 'pass':
          $this->setPassword($value);
        break;

        case 'path':
          $this->setPath($value);
        break;

        case 'query':
          $this->setQueryString($value);
        break;

        case 'fragment':
          $this->setAnchor($value);
        break;
      }
    }
  }

  function getProtocol()
  {
    return $this->protocol;
  }

  function getUser()
  {
    return $this->user;
  }

  function getPassword()
  {
    return $this->password;
  }

  function getHost()
  {
    return $this->host;
  }

  function getPort()
  {
    return $this->port;
  }

  function getPath()
  {
    return $this->path;
  }

  function getAnchor()
  {
    return $this->anchor;
  }

  function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }

  function setUser($user)
  {
    $this->user = $user;
  }

  function setPassword($password)
  {
    $this->password = $password;
  }

  function setHost($host)
  {
    $this->host = $host;
  }

  function setPort($port)
  {
    $this->port = $port;
  }

  function setPath($path)
  {
    $this->path = $path;
    $this->path_elements = explode('/',$this->path);
  }

  function setAnchor($anchor)
  {
    $this->anchor = $anchor;
  }

  function isAbsolute()
  {
    if(!strlen($this->path))
      return true;

    return ('/' == $this->path{0});
  }

  function isRelative()
  {
    return !$this->isAbsolute();
  }

  function countPath()
  {
    return sizeof($this->path_elements);
  }

  function countQueryItems()
  {
    return sizeof($this->query_items);
  }

  function compare($uri)
  {
    return (
          $this->protocol == $uri->getProtocol() &&
          $this->host == $uri->getHost() &&
          $this->port == $uri->getPort() &&
          $this->user === $uri->getUser() &&
          $this->password === $uri->getPassword() &&
          $this->compareQuery($uri) &&
          $this->comparePath($uri) === 0
        );
  }

  function compareQuery($uri)
  {
    if ($this->countQueryItems() != $uri->countQueryItems())
      return false;

    foreach($this->query_items as $name => $value)
    {
      if( (($item = $uri->getQueryItem($name)) === false) ||
          $item != $value)
        return false;
    }
    return true;
  }

  function comparePath($uri)
  {
    $count1 = $this->countPath();
    $count2 = $uri->countPath();
    $iterCount = min($count1, $count2);

    for($i=0; $i < $iterCount; $i++)
    {
      if( $this->getPathElement($i) != $uri->getPathElement($i) )
        return false;
    }

    return ($count1 - $count2);
  }

  function toString($parts = array('protocol', 'user', 'password', 'host', 'port', 'path', 'query', 'anchor'))
  {
    $string = '';

    if(in_array('protocol', $parts))
      $string .= !empty($this->protocol) ? $this->protocol . '://' : '';

    if(in_array('user', $parts))
    {
      $string .=  $this->user;

      if(in_array('password', $parts))
        $string .= (!empty($this->password) ? ':' : '') . $this->password;

      $string .= (!empty($this->user) ? '@' : '');
    }

    if(in_array('host', $parts))
    {
      $string .= $this->host;

      if(in_array('port', $parts))
        $string .= (empty($this->port) ||  ($this->port == '80') ? '' : ':' . $this->port);
    }
    else
      $string = '';

    if(in_array('path', $parts))
      $string .= $this->path;

    if(in_array('query', $parts))
    {
      $query_string = $this->getQueryString();
      $string .= !empty($query_string) ? '?' . $query_string : '';
    }

    if(in_array('anchor', $parts))
      $string .= !empty($this->anchor) ? '#' . $this->anchor : '';

     return $string;
  }

  function getPathElement($level)
  {
    return isset($this->path_elements[$level]) ? $this->path_elements[$level] : '';
  }

  function getPathElements()
  {
    return $this->path_elements;
  }

  function getPathToLevel($level)
  {
    if(!$this->path_elements || $level >= sizeof($this->path_elements))
      return '';

    $items = array();
    for($i = 0; $i <= $level; $i++)
      $items[] = $this->path_elements[$i];

    return implode('/', $items);
  }

  function getPathFromLevel($level)
  {
    if($level <= 0)
      return $this->path;

    if(!$this->path_elements || $level >= sizeof($this->path_elements))
      return '/';

    $items[] = '';

    for($i = $level; $i < sizeof($this->path_elements); $i++)
      $items[] = $this->path_elements[$i];

    return implode('/', $items);
  }


  function addEncodedQueryItem($name, $value)
  {
    $this->query_items[$name] = $value;
  }

  function addQueryItem($name, $value)
  {
    $this->query_items[$name] = is_array($value) ?
      lmbArrayHelper :: arrayMapRecursive('urlencode', $value) :
      urlencode($value);
  }

  function getQueryItem($name)
  {
    if (isset($this->query_items[$name]))
      return $this->query_items[$name];

    return false;
  }

  function getQueryItems()
  {
    return $this->query_items;
  }

  function setQueryItems($items)
  {
    $this->query_items = $items;
  }

  /**
  * Removes a query_string item
  *
  */
  function removeQueryItem($name)
  {
    if (isset($this->query_items[$name]))
      unset($this->query_items[$name]);
  }

  /**
  * Sets the query_string to literally what you supply
  */
  function setQueryString($query_string)
  {
    $this->query_items = $this->_parseQueryString($query_string);
  }

  /**
  * Removes query items
  */
  function removeQueryItems()
  {
    $this->query_items = array();
  }

  /**
  * Returns flat query_string
  *
  */
  function getQueryString()
  {
    $query_string = '';
    $query_items = array();
    $flat_array = array();

    lmbArrayHelper :: toFlatArray($this->query_items, $flat_array);
    ksort($flat_array);
    foreach($flat_array as $key => $value)
    {
      if($value != '' ||  is_null($value))
        $query_items[] = $key . '=' . $value;
      else
        $query_items[] = $key;
    }

    if($query_items)
      $query_string = implode('&', $query_items);

    return $query_string;
  }

  /**
  * Parses raw query_string and returns an array of it
  */
  protected function _parseQueryString($query_string)
  {
    parse_str($query_string, $arr);

    foreach($arr as $key => $item)
    {
      if(!is_array($item))
        $arr[$key] = rawurldecode($item);
    }

    return $arr;
  }

  /**
  * Resolves //, ../ and ./ from a path and returns
  * the result. Eg:
  *
  * /foo/bar/../boo.php    => /foo/boo.php
  * /foo/bar/../../boo.php => /boo.php
  * /foo/bar/.././/boo.php => /foo/boo.php
  *
  */
  function normalizePath()
  {
    $path = $this->path;
    $path = explode('/', preg_replace('~[\/]+~', '/', $path));

    for ($i=0; $i < sizeof($path); $i++)
    {
      if ($path[$i] == '.')
      {
        unset($path[$i]);
        $path = array_values($path);
        $i--;
      }
      elseif ($path[$i] == '..' &&  ($i > 1 ||  ($i == 1 &&  $path[0] != '') ) )
      {
        unset($path[$i]);
        unset($path[$i-1]);
        $path = array_values($path);
        $i -= 2;
      }
      elseif ($path[$i] == '..' &&  $i == 1 &&  $path[0] == '')
      {
        unset($path[$i]);
        $path = array_values($path);
        $i--;
      }
      else
        continue;
    }

    $this->path = implode('/', $path);
    $this->path_elements = explode('/',$this->path);
  }
}

