<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbDSN.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/classkit/src/lmbObject.class.php');

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
    $this->uri = new lmbUri();

    $this->uri->parse($str);
    $this->driver = $this->uri->getProtocol();
    $this->host = $this->uri->getHost();
    $this->user = $this->uri->getUser();
    $this->password = $this->uri->getPassword();
    $this->database = ltrim($this->uri->getPath(), '/');
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

  function get($name)
  {
    $value = parent :: get($name);

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

?>
