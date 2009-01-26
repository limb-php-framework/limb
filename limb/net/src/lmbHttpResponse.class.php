<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/exception/lmbException.class.php');
lmb_require('limb/net/src/lmbHttpRedirectStrategy.class.php');

/**
 * class lmbHttpResponse.
 *
 * @package net
 * @version $Id: lmbHttpResponse.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbHttpResponse
{
  protected $response_string = '';
  protected $response_file_path = '';
  protected $headers = array();
  protected $cookies = array();
  protected $is_redirected = false;
  protected $redirected_path = false;
  protected $redirect_strategy = null;
  protected $transaction_started = false;

  function setRedirectStrategy($strategy)
  {
    $this->redirect_strategy = $strategy;
  }

  function redirect($path)
  {
    $this->_ensureTransactionStarted();

    if ($this->is_redirected)
      return;

    if($this->redirect_strategy === null)
      $strategy = $this->_getDefaultRedirectStrategy();
    else
      $strategy = $this->redirect_strategy;

    $strategy->redirect($this, $path);

    $this->is_redirected = true;
    $this->redirected_path = $path;
  }

  function getRedirectedPath()
  {
    if($this->is_redirected)
      return $this->redirected_path;
    else
      return '';
  }

  function getRedirectedUrl()
  {
    return $this->getRedirectedPath();
  }

  protected function _getDefaultRedirectStrategy()
  {
    lmb_require('limb/net/src/lmbHttpRedirectStrategy.class.php');
    return new lmbHttpRedirectStrategy();
  }

  function reset()
  {
    $this->response_string = '';
    $this->response_file_path = '';
    $this->headers = array();
    $this->is_redirected = false;
    $this->transaction_started = false;
  }

  function start()
  {
    $this->reset();
    $this->transaction_started = true;
  }

  function isRedirected()
  {
    return $this->is_redirected;
  }

  function getStatus()
  {
    $status = null;
    foreach($this->headers as $header)
    {
      if(preg_match('~^HTTP/1.\d[^\d]+(\d+)[^\d]*~i', $header, $matches))
        $status = (int)$matches[1];
    }

    if($status)
      return $status;
    else
      return 200;
  }

  function getDirective($directive_name)
  {
    $directive = null;
    $regex = '~^' . preg_quote($directive_name). "\s*:(.*)$~i";
    foreach($this->headers as $header)
    {
      if(preg_match($regex, $header, $matches))
        $directive = trim($matches[1]);
    }

    if($directive)
      return $directive;
    else
      return false;
  }

  function getContentType()
  {
    if($directive = $this->getDirective('content-type'))
    {
      list($type, ) = explode(';', $directive);
      return trim($type);
    }
    else
      return 'text/html';
  }

  function getMimeType()
  {
    return $this->getContentType();
  }

  function getResponseString()
  {
    return $this->response_string;
  }

  function isStarted()
  {
    return $this->transaction_started;
  }

  function isEmpty()
  {
    $status = $this->getStatus();

    return (
      !$this->is_redirected &&
      empty($this->response_string) &&
      empty($this->response_file_path) &&
      ($status != 304 &&  $status != 412));//???
  }

  function isHeadersSent()
  {
    return sizeof($this->headers) > 0;
  }

  /**
   *@deprecated
   *@see self::isHeadersSent()
   *@return bool
   */
  function headersSent()
  {
    return $this->isHeadersSent();
  }

  function isFileSent()
  {
    return !empty($this->response_file_path);
  }

  /**
   *@deprecated
   *@see self::isFileSent()
   *@return bool
   */
  function fileSent()
  {
    return $this->isFileSent();
  }

  function reload()
  {
    $this->redirect($_SERVER['PHP_SELF']);
  }


  /**
   * Add header
   * @param string $header
   */
  function addHeader($header)
  {
    $this->_ensureTransactionStarted();

    $this->headers[] = $header;
  }

  /**
   * Add header
   * @deprecated
   * @see self::addHeader
   * @param string $header
   */
  function header($header)
  {
    $this->addHeader($header);
  }

  function setCookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false)
  {
    $this->_ensureTransactionStarted();

    $this->cookies[$name] = array('name' => $name,
                             'value' => $value,
                             'expire' => $expire,
                             'path' => $path,
                             'domain' => $domain,
                             'secure' => $secure);
  }

  function getCookies()
  {
    return $this->cookies;
  }

  function removeCookie($name, $path = '/', $domain = '', $secure = false)
  {

    if(isset($this->cookies[$name]))
    {
      $path  = $this->cookies[$name]['path'];
      $domain  = $this->cookies[$name]['domain'];
      $secure  = $this->cookies[$name]['secure'];

      unset($this->cookies[$name]);
    }

    $this->setCookie($name, '', 1, $path, $domain, $secure);
  }

  function readFile($file_path)
  {
    $this->_ensureTransactionStarted();

    $this->response_file_path = $file_path;
  }

  function write($string)
  {
    $this->_ensureTransactionStarted();

    $this->response_string = $string;
  }

  function append($string)
  {
    $this->_ensureTransactionStarted();

    $this->response_string .= $string;
  }

  function commit()
  {
    $this->_ensureTransactionStarted();

    foreach($this->headers as $header)
      $this->_sendHeader($header);

    foreach($this->cookies as $cookie)
      $this->_sendCookie($cookie);

    if(!empty($this->response_file_path))
      $this->_sendFile($this->response_file_path);
    else if(!empty($this->response_string))
      $this->_sendString($this->response_string);

    $this->transaction_started = false;
  }

  protected function _sendHeader($header)
  {
    header($header);
  }

  protected function _sendCookie($cookie)
  {
    setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure']);
  }

  protected function _sendString($string)
  {
    echo $string;
  }

  protected function _sendFile($file_path)
  {
    readfile($file_path);
  }

  protected function _ensureTransactionStarted()
  {
    if(!$this->transaction_started)
      $this->start();
  }
}
