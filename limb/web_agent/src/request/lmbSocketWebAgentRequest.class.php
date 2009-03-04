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
 * Web request with sockets
 *
 * @package web_agent
 * @version $Id: lmbSocketWebAgentRequest.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbSocketWebAgentRequest extends lmbAbstractWebAgentRequest {
  protected $request_data = '';
  protected $request_headers = null;
  protected $request_method = '';
  protected $parsed_url = null;
  protected $response_data = array();
  protected $response_headers = null;

  protected function prepareRequestData($url, $method)
  {
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
    $this->request_method = strtoupper($method);
  }

  protected function setRequestUrl($url)
  {
    $this->parsed_url = parse_url($url);
  }

  protected function getRequestHostWithPort()
  {
    $host = $this->getRequestHost();
    $port = $this->getRequestPort();
    if($port != 80) $host .= ':'.$port;
    return $host;
  }

  protected function getRequestPort()
  {
  	return isset($this->parsed_url['port']) ? $this->parsed_url['port'] : 80;
  }

  protected function getRequestHost()
  {
  	return $this->parsed_url['host'];
  }

  protected function getRequestPath()
  {
    $path = '/';
    if(isset($this->parsed_url['path']))
      $path = $this->parsed_url['path'];
    if(isset($this->parsed_url['query']))
      $path .= '?'.$this->parsed_url['query'];
    return $path;
  }

  protected function initRequestData()
  {
  	$this->request_headers = new lmbWebAgentHeaders();
    $this->request_headers->setRaw(
      $this->request_method.' '.$this->getRequestPath().' HTTP/1.1'
    );
    $this->addHeader('host', $this->getRequestHostWithPort());
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
      $this->addHeader('User-Agent', $this->user_agent);
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
      $this->addHeader('Content-length', strlen($this->content));
  }

  protected function assembleRequestData()
  {
    $this->request_data = $this->request_headers->exportHeaders()."\r\n".$this->content;
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
    if($fp = fsockopen($this->getRequestHost(), $this->getRequestPort()))
    {
      fwrite($fp, $this->request_data);
      $this->response_data = array();
      while(!feof($fp))
      	$this->response_data[] = fgets($fp);
      fclose($fp);
      return true;
    }
    else
      return false;
  }

  protected function readHeaders()
  {
    $this->response_headers = new lmbWebAgentHeaders();
  	while($header = trim(array_shift($this->response_data))) {
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
    return implode('', $this->response_data);
  }
}
