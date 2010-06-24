<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Web agent headers
 *
 * @package web_agent
 * @version $Id: lmbWebAgentHeaders.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebAgentHeaders {

  const last = -1;

  protected $headers = array();

  function __construct($headers = array())
  {
    $this->import($headers);
  }

  function set($name, $value)
  {
    $this->setRaw(strtolower($name), $value);
  }

  function setRaw($name, $value = null)
  {
    if(!$this->has($name))
      $this->headers[$name] = array();
    $this->headers[$name][] = $value;
  }

  function get($name, $num = self::last)
  {
    if(!$this->has($name))
      return null;
    if($num == self::last)
      return end($this->headers[$name]);
    if(isset($this->headers[$name][$num]))
      return $this->headers[$name][$num];
    return null;
  }

  function countHeaders($name)
  {
    if(!$this->has($name))
      return 0;
    return count($this->headers[$name]);
  }

  function getFirst()
  {
    if(!$this->headers)
      return null;
    $keys = array_keys($this->headers);
    $first = $keys[0];
    $value = $this->get($first);
    if($value === null)
      return $first;
    return $value;
  }

  function has($name)
  {
    return isset($this->headers[$name]);
  }

  function import($headers)
  {
  	foreach($headers as $name => $value)
    {
      if($value === null)
        $this->setRaw($name);
      else
        $this->set($name, $value);
    }
  }

  function parse($header)
  {
    $header = trim($header);
    $expl = explode(':', $header, 2);
    $name = trim($expl[0]);
    if(!$name) return false;
    if(count($expl) > 1)
      $this->set($name, trim($expl[1]));
    else
      $this->setRaw($name);
    return true;
  }

  function exportHeader($name, $num = self::last)
  {
    $value = $this->get($name, $num);
    if($value === null) return null;
    $name = self::normalizeName($name);
    return $name.': '.$value;
  }

  function exportHeaders()
  {
    $result = array();
    $names = array_keys($this->headers);
    foreach($names as $name)
    {
    	$n = 0;
      while(($header = $this->exportHeader($name, $n ++)) !== null)
      {
      	$result[] = $header;
      }
      if($n == 1)
        $result[] = $name;
    }
    return implode("\r\n", $result)."\r\n";
  }

  function remove($name)
  {
    if($this->has($name))
      unset($this->headers[$name]);
  }

  function copyTo(lmbWebAgentHeaders $dest)
  {
    foreach($this->headers as $name => $values)
    {
    	foreach($values as $value)
        $dest->setRaw($name, $value);
    }
  }

  function clean()
  {
    $this->headers = array();
  }

  static function normalizeName($name)
  {
  	$new_name = array();
    foreach(explode('-', $name) as $piece)
    {
    	$new_name[] = ucfirst($piece);
    }
    return implode('-', $new_name);
  }
}
