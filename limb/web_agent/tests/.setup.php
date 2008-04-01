<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once(dirname(__FILE__) . '/../common.inc.php');
lmb_require('limb/web_agent/src/request/lmbAbstractWebAgentRequest.class.php');
lmb_require('limb/web_agent/src/lmbWebServerResponse.class.php');
lmb_require('limb/web_agent/src/lmbWebAgentCookies.class.php');
lmb_require('limb/web_agent/src/lmbWebServerCookiesCollection.class.php');

/**
 * @package web_agent
 * @version $Id: .setup.php 43 2007-10-05 15:33:11Z CatMan $
 */
class lmbFakeWebAgentRequest extends lmbAbstractWebAgentRequest 
{

  public $response_content = '';
  public $response_status = 200;
  public $response_mediatype = '';
  public $response_charset = '';
  public $response_headers;
  public $response_cookies;

  public $request_cookies;
  public $request_content = '';
  public $request_url = '';
  public $request_accept_charset = '';

  function __construct()
  {
    parent::__construct();
    $this->response_headers = new lmbWebAgentHeaders();
    $this->response_cookies = new lmbWebServerCookiesCollection();
  }

  function doRequest($url, $method = 'GET')
  {
    $this->request_cookies = clone $this->cookies;
    $this->request_content = $this->content;
    $this->request_accept_charset = $this->accept_charset;
    $this->request_url = $url;
    return new lmbWebServerResponse(
      $this->response_content,
      $this->response_status,
      $this->response_mediatype,
      $this->response_charset,
      $this->response_cookies,
      $this->response_headers
    );
  }
}

