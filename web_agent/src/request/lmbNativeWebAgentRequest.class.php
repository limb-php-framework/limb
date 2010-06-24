<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/lmbAbstractWebAgentRequest.class.php');
lmb_require(dirname(__FILE__).'/../lmbWebServerResponse.class.php');
lmb_require(dirname(__FILE__).'/../lmbWebAgentCookie.class.php');
lmb_require(dirname(__FILE__).'/../lmbWebAgentHeaders.class.php');
lmb_require(dirname(__FILE__).'/../lmbWebServerCookiesCollection.class.php');

/**
 * Web request through file_get_contents
 *
 * @package web_agent
 * @version $Id: lmbNativeWebAgentRequest.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbNativeWebAgentRequest extends lmbAbstractWebAgentRequest {
  protected $request_data = null;
  protected $request_headers = null;
  protected $url = null;
  protected $response_data = false;
  protected $response_headers_raw = '';
  protected $response_headers = null;
  
  protected function prepareRequestData($url, $method)
  {
    $this->request_data = array('http' => array());
    $this->setRequestMethod($method);
    $this->setRequestUrl($url);
    $this->initRequestData();
    $this->prepareCookies();
    $this->prepareUserAgent();
    $this->prepareAcceptCharset();
    $this->prepareHeaders();
    $this->prepareContent();
    $this->assembleRequestData();
  }

  protected function setRequestMethod($method)
  {
    $this->request_data['http']['method'] = $method;
  }

  protected function setRequestUrl($url)
  {
    $this->url = $url;
  }

  protected function initRequestData()
  {
  	$this->request_headers = new lmbWebAgentHeaders();
    $this->addHeader('connection', 'close');
  }

  protected function addHeader($name, $value)
  {
  	$this->request_headers->set($name, $value);
  }

  protected function prepareCookies()
  {
    if($this->cookies->hasCookies())
      $this->addHeader('Cookie', $this->cookies->export());
  }

  protected function prepareUserAgent()
  {
    if($this->user_agent)
      $this->request_data['http']['user_agent'] = $this->user_agent;
  }

  protected function prepareAcceptCharset()
  {
    if($this->accept_charset)
      $this->addHeader('Accept-Charset', $this->accept_charset);
  }

  protected function prepareHeaders()
  {
    $this->headers->copyTo($this->request_headers);
  }

  protected function prepareContent()
  {
    if($this->content)
      $this->request_data['http']['content'] = $this->content;
  }

  protected function assembleRequestData()
  {
    $this->request_data['http']['header'] = $this->request_headers->exportHeaders();
  }

  function doRequest($url, $method = 'GET')
  {
    $this->prepareRequestData($url, $method);
    //echo '<pre>', $this->request_data, '</pre>';
    if($this->readData())
    {
      $headers =  $this->readHeaders();
      $status = $this->readStatus();
      $mediatype = $this->readMediaType();
      $charset = $this->readCharset();
      $cookies = $this->readCookies();
      $content = $this->readContent();
      /*ini_set('xdebug.var_display_max_depth', 4 );
      var_dump(
        $status,
        $mediatype,
        $charset,
        $cookies,
        $headers
       );
      echo $content;*/
      return new lmbWebServerResponse(
        $content,
        $status,
        $mediatype,
        $charset,
        $cookies,
        $headers
      );
    }
    else
    {
      return new lmbWebServerResponse('', 400, '', '', new lmbWebServerCookiesCollection(), new lmbWebAgentHeaders());
    }
  }

  protected function readData()
  {
    $context  = stream_context_create($this->request_data);
    $this->response_data = file_get_contents($this->url, null, $context);
    if($this->response_data === false)
      return false;
    
    $this->response_headers_raw = $http_response_header;
    return true;
  }
  
  protected function readHeaders()
  {
    $this->response_headers = new lmbWebAgentHeaders();
    foreach($this->response_headers_raw as $header)
    {
      $this->response_headers->parse($header);
    }
    return $this->response_headers;
  }

  protected function readStatus()
  {
    $first = $this->response_headers->getFirst();
    if(!$first) return 400;
    if(!preg_match('#^HTTP/\S+[ ]+([0-9]+)#', $first, $a))
      return false;
    return $a[1];
  }

  protected function getContentTypeHeader()
  {
    return strtolower($this->response_headers->get('content-type'));
  }

  protected function readMediaType()
  {
    $content_type = $this->getContentTypeHeader();
    if(!$content_type)
      return 'text/html';
    return substr($content_type, 0, strpos($content_type, ';'));
  }

  protected function readCharset()
  {
    $content_type = $this->getContentTypeHeader();
    if(!$content_type)
      return $this->getDefaultCharset();

    $charset_pos = strpos($content_type, 'charset=');
    if($charset_pos === false)
      return $this->getDefaultCharset();
      
    return substr($content_type, $charset_pos + 8);
  }

  protected function readCookies()
  {
    $cookies = new lmbWebServerCookiesCollection();
    $n = 0;
    while(($value = $this->response_headers->get('set-cookie', $n ++)) !== null)
    {
    	$cookies->add(new lmbWebServerCookie($value));
    }
    return $cookies;
  }

  protected function readContent()
  {
    return $this->response_data;
  }
}
