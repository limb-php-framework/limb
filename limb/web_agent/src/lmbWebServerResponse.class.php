<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/lmbWebServerCookiesCollection.class.php');
lmb_require(dirname(__FILE__).'/lmbWebAgentHeaders.class.php');

/**
 * Web server response
 *
 * @package web_agent
 * @version $Id: lmbWebServerResponse.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebServerResponse {
  protected $content;
  protected $status;
  protected $mediatype;
  protected $charset;
  protected $headers;
  protected $cookies;

  function __construct($content, $status, $mediatype, $charset, lmbWebServerCookiesCollection $cookies, lmbWebAgentHeaders $headers)
  {
    $this->content = $content;
    $this->status = $status;
    $this->mediatype = $mediatype;
    $this->charset = $charset;
    $this->headers = $headers;
    $this->cookies = $cookies;
  }

  function getContent()
  {
    return $this->content;
  }

  function getStatus()
  {
    return $this->status;
  }

  function getMediaType()
  {
    return $this->mediatype;
  }

  function getCharset()
  {
    return $this->charset;
  }

  function getCookies()
  {
    return $this->cookies;
  }

  function getHeaders()
  {
    return $this->headers;
  }

}
