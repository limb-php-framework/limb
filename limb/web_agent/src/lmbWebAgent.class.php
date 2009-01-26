<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_agent
 */
lmb_require(dirname(__FILE__).'/lmbWebAgentKit.class.php');
lmb_require(dirname(__FILE__).'/lmbWebServerCookiesCollection.class.php');
lmb_require(dirname(__FILE__).'/lmbWebAgentValues.class.php');

/**
 * Web agent
 *
 * @package web_agent
 * @version $Id: lmbWebAgent.class.php 89 2007-10-12 15:28:50Z CatMan $
 */
class lmbWebAgent {
  protected $request;
  protected $cookies;
  protected $values = null;
  protected $response = null;
  protected $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7';
  protected $content = '';
  protected $accept_charset = 'utf-8,windows-1251;q=0.7,*;q=0.7';
  protected $charset = 'utf-8';

  function __construct($request = null)
  {
    $this->cookies = new lmbWebServerCookiesCollection();
    $this->values = new lmbWebAgentValues();

    if($request)
      $this->request = $request;
    else
      $this->request = lmbWebAgentKit::createRequest();
  }

  function getCookies()
  {
  	return $this->cookies;
  }

  function getValues()
  {
  	return $this->values;
  }

  function getResponse()
  {
    return $this->response;
  }

  function getContent()
  {
    return $this->content;
  }

  function setUserAgent($user_agent)
  {
    $this->user_agent = $user_agent;
  }

  function setAcceptCharset($accept_charset)
  {
    $this->accept_charset = $accept_charset;
  }

  function setCharset($charset)
  {
    $this->charset = $charset;
  }

  function getCharset()
  {
    return $this->charset;
  }

  function doRequest($url, $method = 'GET', $max_redirects = 5, $content = null)
  {
    $this->prepareRequest($url, $method, $content);
    $this->response = $this->request->doRequest($url, $method);
    $this->analyzeResponse($url);
    $this->doRedirect($max_redirects);
  }

  function doRedirect($max_redirects = 5)
  {
  	if(!$max_redirects || !$this->response->getHeaders()->has('location')) return;
    $location = $this->response->getHeaders()->get('location');
    $bu_vals = $this->values;
    $this->values = new lmbWebAgentValues();
    $this->doRequest($location, 'GET', $max_redirects - 1);
    $this->values = $bu_vals;
  }

  protected function prepareRequest(&$url, $method, $content)
  {
    $this->request->clean();
    $this->request->setUserAgent($this->user_agent);
    $this->request->setAcceptCharset($this->accept_charset);
    $this->prepareCookies($url);
    $this->prepareContent($url, $method, $content);
  }

  protected function prepareContent(&$url, $method, $content)
  {
    $encoding = $this->charset ? $this->charset : 'utf-8';
    $query = $this->values->buildQuery($encoding);
    if($method == 'GET')
    {
      if($query)
        $url .= '?'.$query;
    }
    elseif($method == 'POST')
    {
      if($content === null)
        $content = $query;
    }
    if($content)
    {
      if($method != 'POST' && $encoding != 'utf-8')
        $content = mb_convert_encoding($content, $encoding, 'utf-8');
      $this->request->setContent($content);
      if($method == 'POST')
        $this->request->getHeaders()->set('content-type', 'application/x-www-form-urlencoded');
    }
  }

  protected function prepareCookies($url)
  {
    $url = parse_url($url);
    foreach($this->cookies as $cookie)
    {
      if(strpos($cookie->domain, '.') === 0)
      {
        if(substr($url['host'], -strlen($cookie->domain) + 1) != substr($cookie->domain, 1))
          continue;
      }
      elseif($cookie->domain != $url['host'])
        continue;
      $path = isset($url['path']) ? $url['path'] : '/';
      if(strpos($path, $cookie->path) !== 0)
        continue;
      $this->request->getCookies()->set($cookie->name, $cookie->value);
    }
  }

  protected function analyzeResponse($url)
  {
    $this->analyzeCookies($url);
    $this->analyzeContent();
  }

  protected function analyzeCookies($url)
  {
    $url = parse_url($url);
    foreach($this->response->getCookies() as $cookie)
    {
      $newcookie = clone $cookie;
      if(!$newcookie->domain)
        $newcookie->domain = $url['host'];
      if(!$newcookie->path)
        $newcookie->path = isset($url['path']) ? $url['path'] : '/';
      $this->getCookies()->add($newcookie);
    }
  }

  protected function analyzeContent()
  {
    $this->charset = $this->response->getCharset();
    $this->content = $this->response->getContent();
    if($this->charset && $this->charset != 'utf-8')
      $this->content = mb_convert_encoding($this->content, 'utf-8', $this->charset);
  }
}
