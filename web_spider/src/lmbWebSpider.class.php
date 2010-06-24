<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_spider/src/lmbUriFilter.class.php');
lmb_require('limb/web_spider/src/lmbContentTypeFilter.class.php');
lmb_require('limb/web_spider/src/lmbUriExtractor.class.php');
lmb_require('limb/web_spider/src/lmbUriNormalizer.class.php');
lmb_require('limb/web_spider/src/lmbUriContentReader.class.php');

/**
 * class lmbWebSpider.
 *
 * @package web_spider
 * @version $Id: lmbWebSpider.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbWebSpider
{
  protected $uri_extractor;
  protected $content_reader;
  protected $uri_filter;
  protected $uri_normalizer;

  protected $observers = array();

  protected $uri_cache = array();

  function crawl($uri)
  {
    if($uri->getHost() == '')//???
      return false;

    $this->_crawlRecursive($uri, $uri);

    return true;
  }

  function _crawlRecursive($uri, $context_uri)
  {
    $this->_normalizeUriUsingContext($uri, $context_uri);

    $this->getUriNormalizer()->process($uri);

    if($this->_isCacheHit($uri))
      return;

    $this->_markCached($uri);

    if(!$this->getUriFilter()->canPass($uri))
      return;

    $reader = $this->getUriContentReader();
    $reader->open($uri);

    if(!$this->getContentTypeFilter()->canPass($reader->getContentType()))
      return;

    $this->_notifyObservers();

    $links = $this->getUriExtractor()->extract($reader->getContent());

    foreach(array_keys($links) as $key)
    {
      $this->_crawlRecursive($links[$key], $uri);
    }
  }

  function _normalizeUriUsingContext($uri, $context_uri)
  {
    if(!$uri->getHost())
    {
      $uri->setHost($context_uri->getHost());

      if(($path = $context_uri->getPath()) && $uri->isRelative())
      {
        $path = preg_replace('~(.*)(/[^/]*)$~', '$1/', $path);
        $uri->setPath($path . $uri->getPath());
      }
    }

    if(!$uri->getProtocol())
      $uri->setProtocol($context_uri->getProtocol());

    $uri->setAnchor('');

    $uri->normalizePath();
  }

  function _isCacheHit($uri)
  {
    return isset($this->uri_cache[$uri->toString()]);
  }

  function _markCached($uri)
  {
    $this->uri_cache[$uri->toString()] = 1;
  }

  function _notifyObservers()
  {
    foreach(array_keys($this->observers) as $key)
      $this->observers[$key]->notify($this->content_reader);
  }

  function registerObserver(&$observer)
  {
    $this->observers[] =& $observer;
  }

  function getUriExtractor()
  {
    if(is_object($this->uri_extractor))
      return $this->uri_extractor;

    include_once(dirname(__FILE__) . '/lmbUriExtractor.class.php');
    $this->uri_extractor = new lmbUriExtractor();
    return $this->uri_extractor;
  }

  function setUriExtractor($extractor)
  {
    $this->uri_extractor = $extractor;
  }

  function getUriContentReader()
  {
    if(is_object($this->content_reader))
      return $this->content_reader;

    include_once(dirname(__FILE__) . '/lmbUriContentReader.class.php');
    $this->content_reader = new lmbUriContentReader();
    return $this->content_reader;
  }

  function setUriContentReader($reader)
  {
    $this->content_reader = $reader;
  }

  function getContentTypeFilter()
  {
    if(is_object($this->content_type_filter))
      return $this->content_type_filter;

    include_once(dirname(__FILE__) . '/lmbContentTypeFilter.class.php');
    $this->content_type_filter = new lmbContentTypeFilter();
    return $this->content_type_filter;
  }

  function setContentTypeFilter($filter)
  {
    $this->content_type_filter = $filter;
  }

  function setUriFilter($filter)
  {
    $this->uri_filter = $filter;
  }

  function getUriFilter()
  {
    if(is_object($this->uri_filter))
      return $this->uri_filter;

    include_once(dirname(__FILE__) . '/lmbUriFilter.class.php');
    $this->uri_filter = new lmbUriFilter();
    return $this->uri_filter;
  }

  function setUriNormalizer($normalizer)
  {
    $this->uri_normalizer = $normalizer;
  }

  function getUriNormalizer()
  {
    if(is_object($this->uri_normalizer))
      return $this->uri_normalizer;

    include_once(dirname(__FILE__) . '/lmbUriNormalizer.class.php');
    $this->uri_normalizer = new lmbUriNormalizer();
    return $this->uri_normalizer;
  }
}


