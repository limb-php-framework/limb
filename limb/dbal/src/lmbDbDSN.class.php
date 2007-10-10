<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/core/src/lmbObject.class.php');

/**
 * class lmbDbDSN.
 *
 * @package dbal
 * @version $Id: lmbDbDSN.class.php 6405 2007-10-10 04:57:08Z pachanga $
 */
class lmbDbDSN extends lmbObject
{
  protected $uri;
  protected $extra = array();

  function __construct($args)
  {
    if(is_array($args))
    {
      foreach($args as $key => $value)
      {
        if(is_numeric($key) && is_array($value))
          $this->extra = $value;
        else
          $this->$key = $value;
      }
    }
    elseif(is_string($args))
      $this->_parseUri($args);
  }

  function _parseUri($str)
  {
    try
    {
      $this->uri = new lmbUri($str);
    }
    catch(lmbException $e)
    {
      throw new lmbException("Database DSN '$str' is not valid");
    }

    $this->driver = $this->uri->getProtocol();
    $this->host = $this->uri->getHost();
    $this->user = $this->uri->getUser();
    $this->password = $this->uri->getPassword();
    $this->database = substr($this->uri->getPath(), 1);//removing only first slash
    $this->port = $this->uri->getPort();
    $this->extra = $this->uri->getQueryItems();
  }

  function _getUri()
  {
    if(is_object($this->uri))
      return $this->uri;

    $this->uri = new lmbUri();
    $this->uri->setProtocol($this->driver);
    $this->uri->setHost($this->host);
    $this->uri->setUser($this->user);
    $this->uri->setPassword($this->password);
    $this->uri->setPath('/' . $this->database);

    if(isset($this->port))
      $this->uri->setPort($this->port);
    if(count($this->extra))
      $this->uri->setQueryItems($this->extra);

    return $this->uri;
  }

  protected function _getRaw($name)
  {
    $value = parent :: _getRaw($name);

    if(isset($value))
      return $value;

    if(isset($this->extra[$name]))
      return $this->extra[$name];
  }

  function toString()
  {
    return $this->_getUri()->toString();
  }
}


