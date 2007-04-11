<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUri.class.php 5621 2007-04-11 09:36:16Z pachanga $
 * @package    net
 */
lmb_require('limb/datasource/src/lmbComplexArray.class.php');

class lmbUri
{
  protected $_protocol = '';
  protected $_user = '';
  protected $_password = '';
  protected $_host = '';
  protected $_port = '';
  protected $_path = '';
  protected $_anchor = '';
  protected $_query_items = array();
  protected $_path_elements = array();

  function __construct($str='')
  {
    if($str)
      $this->reset($str);
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

  function reset($str = null)
  {
    $this->_user        = '';
    $this->_password    = '';
    $this->_host        = '';
    $this->_port        = '';
    $this->_path        = '';
    $this->_query_items = array();
    $this->_anchor      = '';
    $this->_path_elements = array();

    if(!$str)
      return;

    if(!($urlinfo = @parse_url($str)))
      return;

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
    return $this->_protocol;
  }

  function getUser()
  {
    return $this->_user;
  }

  function getPassword()
  {
    return $this->_password;
  }

  function getHost()
  {
    return $this->_host;
  }

  function getPort()
  {
    return $this->_port;
  }

  function getPath()
  {
    return $this->_path;
  }

  function getAnchor()
  {
    return $this->_anchor;
  }

  function setProtocol($protocol)
  {
    $this->_protocol = $protocol;
  }

  function setUser($user)
  {
    $this->_user = $user;
  }

  function setPassword($password)
  {
    $this->_password = $password;
  }

  function setHost($host)
  {
    $this->_host = $host;
  }

  function setPort($port)
  {
    $this->_port = $port;
  }

  function setPath($path)
  {
    $this->_path = $path;
    $this->_path_elements = explode('/',$this->_path);
  }

  function setAnchor($anchor)
  {
    $this->_anchor = $anchor;
  }

  function isAbsolute()
  {
    if(!strlen($this->_path))
      return true;

    return ('/' == $this->_path{0});
  }

  function isRelative()
  {
    return !$this->isAbsolute();
  }

  //obsolete
  function parse($uri)
  {
    $this->reset($uri);
  }

  function countPath()
  {
    return sizeof($this->_path_elements);
  }

  function countQueryItems()
  {
    return sizeof($this->_query_items);
  }

  function compare($uri)
  {
    return (
          $this->_protocol == $uri->getProtocol() &&
          $this->_host == $uri->getHost() &&
          $this->_port == $uri->getPort() &&
          $this->_user === $uri->getUser() &&
          $this->_password === $uri->getPassword() &&
          $this->compareQuery($uri) &&
          $this->comparePath($uri) === 0
        );
  }

  function compareQuery($uri)
  {
    if ($this->countQueryItems() != $uri->countQueryItems())
      return false;

    foreach($this->_query_items as $name => $value)
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
      $string .= !empty($this->_protocol) ? $this->_protocol . '://' : '';

    if(in_array('user', $parts))
    {
      $string .=  $this->_user;

      if(in_array('password', $parts))
        $string .= (!empty($this->_password) ? ':' : '') . $this->_password;

      $string .= (!empty($this->_user) ? '@' : '');
    }

    if(in_array('host', $parts))
    {
      $string .= $this->_host;

      if(in_array('port', $parts))
        $string .= (empty($this->_port) ||  ($this->_port == '80') ? '' : ':' . $this->_port);
    }
    else
      $string = '';

    if(in_array('path', $parts))
      $string .= $this->_path;

    if(in_array('query', $parts))
    {
      $query_string = $this->getQueryString();
      $string .= !empty($query_string) ? '?' . $query_string : '';
    }

    if(in_array('anchor', $parts))
      $string .= !empty($this->_anchor) ? '#' . $this->_anchor : '';

     return $string;
  }

  function getPathElement($level)
  {
    return isset($this->_path_elements[$level]) ? $this->_path_elements[$level] : '';
  }

  function getPathElements()
  {
    return $this->_path_elements;
  }

  function getPathToLevel($level)
  {
    if(!$this->_path_elements || $level >= sizeof($this->_path_elements))
      return '';

    $items = array();
    for($i = 0; $i <= $level; $i++)
      $items[] = $this->_path_elements[$i];

    return implode('/', $items);
  }

  function getPathFromLevel($level)
  {
    if($level <= 0)
      return $this->_path;

    if(!$this->_path_elements || $level >= sizeof($this->_path_elements))
      return '/';

    $items[] = '';

    for($i = $level; $i < sizeof($this->_path_elements); $i++)
      $items[] = $this->_path_elements[$i];

    return implode('/', $items);
  }


  function addEncodedQueryItem($name, $value)
  {
    $this->_query_items[$name] = $value;
  }

  function addQueryItem($name, $value)
  {
    $this->_query_items[$name] = is_array($value) ?
      lmbComplexArray :: arrayMapRecursive('urlencode', $value) :
      urlencode($value);
  }

  function getQueryItem($name)
  {
    if (isset($this->_query_items[$name]))
      return $this->_query_items[$name];

    return false;
  }

  function getQueryItems()
  {
    return $this->_query_items;
  }

  function setQueryItems($items)
  {
    $this->_query_items = $items;
  }

  /**
  * Removes a query_string item
  *
  */
  function removeQueryItem($name)
  {
    if (isset($this->_query_items[$name]))
      unset($this->_query_items[$name]);
  }

  /**
  * Sets the query_string to literally what you supply
  */
  function setQueryString($query_string)
  {
    $this->_query_items = $this->_parseQueryString($query_string);
  }

  /**
  * Removes query items
  */
  function removeQueryItems()
  {
    $this->_query_items = array();
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

    lmbComplexArray :: toFlatArray($this->_query_items, $flat_array);
    ksort($flat_array);
    foreach($flat_array as $key => $value)
    {
      if ($value != '' ||  is_null($value))
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
    $path = $this->_path;
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

    $this->_path = implode('/', $path);
    $this->_path_elements = explode('/',$this->_path);
  }
}
?>