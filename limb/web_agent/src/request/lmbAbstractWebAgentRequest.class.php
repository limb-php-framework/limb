<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../lmbWebAgentCookies.class.php');
lmb_require(dirname(__FILE__).'/../lmbWebAgentHeaders.class.php');

/**
 * Abstract class of webagent request
 *
 * @package web_agent
 * @version $Id: lmbAbstractWebAgentRequest.class.php 7417 2008-12-19 07:25:26Z conf $
 */
abstract class lmbAbstractWebAgentRequest
{
  protected $cookies;
  protected $user_agent = '';
  protected $accept_charset = '';
  protected $headers;
  protected $content = '';
  protected $default_charset = 'utf-8';

  function __construct()
  {
  	$this->cookies = new lmbWebAgentCookies();
    $this->headers = new lmbWebAgentHeaders();
  }

  function getCookies()
  {
  	return $this->cookies;
  }
  
  function getDefaultCharset()
  {
    return $this->default_charset;
  }

  function setUserAgent($user_agent)
  {
    $this->user_agent = $user_agent;
  }

  function setAcceptCharset($accept_charset)
  {
    $this->accept_charset = $accept_charset;
  }

  function getHeaders()
  {
  	return $this->headers;
  }

  function setContent($content)
  {
    $this->content = $content;
  }
  
  function setDefaultCharset($charset)
  {
    $this->default_charset = $charset;
  }

  function clean()
  {
    $this->cookies->clean();
    $this->user_agent = '';
    $this->accept_charset = '';
    $this->headers->clean();
    $this->content = '';
  }

  abstract function doRequest($url, $method = 'GET');

}

