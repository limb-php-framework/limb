<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/web_spider/src/lmbWebSpider.class.php');
lmb_require('limb/web_spider/src/lmbContentTypeFilter.class.php');
lmb_require('limb/web_spider/src/lmbUriFilter.class.php');
lmb_require('limb/web_spider/src/lmbUriExtractor.class.php');
lmb_require('limb/web_spider/src/lmbUriNormalizer.class.php');
lmb_require('limb/web_spider/src/lmbUriContentReader.class.php');

class TestingSpiderObserver
{
  function notify($reader){}
}

Mock :: generate('TestingSpiderObserver', 'MockWebSpiderObserver');
Mock :: generate('lmbUriExtractor', 'MockUriExtractor');
Mock :: generate('lmbUriNormalizer', 'MockUriNormalizer');
Mock :: generate('lmbUriFilter', 'MockUriFilter');
Mock :: generate('lmbContentTypeFilter', 'MockContentTypeFilter');
Mock :: generate('lmbUriContentReader', 'MockUriContentReader');

class lmbWebSpiderTest extends UnitTestCase
{
  var $spider;
  var $observer;
  var $extractor;
  var $uri_filter;
  var $content_type_filter;
  var $normalizer;
  var $reader;

  function setUp()
  {
    $this->observer = new MockWebSpiderObserver();
    $this->extractor = new MockUriExtractor();
    $this->uri_filter = new MockUriFilter();
    $this->content_type_filter = new MockContentTypeFilter();
    $this->normalizer = new MockUriNormalizer();
    $this->reader = new MockUriContentReader();

    $this->spider = new lmbWebSpider();
    $this->spider->registerObserver($this->observer);
    $this->spider->setUriExtractor($this->extractor);
    $this->spider->setUriFilter($this->uri_filter);
    $this->spider->setContentTypeFilter($this->content_type_filter);
    $this->spider->setUriNormalizer($this->normalizer);
    $this->spider->setUriContentReader($this->reader);
  }

  function tearDown()
  {
  }

  function testContentTypeFiltering()
  {
    $uri = new lmbUri('http://some.host/whatever.html');

    $this->uri_filter->expectOnce('canPass');
    $this->uri_filter->setReturnValue('canPass', true, array($uri));
    $this->normalizer->expectOnce('process');

    $this->reader->expectOnce('open', array($uri));
    $this->reader->setReturnValue('getContentType', $content_type = 'whatever');
    $this->reader->expectNever('getContent');

    $this->content_type_filter->expectOnce('canPass', array($content_type));
    $this->content_type_filter->setReturnValue('canPass', false, array($content_type));

    $this->extractor->expectNever('extract');
    $this->observer->expectNever('notify');

    $this->spider->crawl($uri);
  }

  function testNotifyObservers()
  {
    $one_more_observer = new MockWebSpiderObserver($this);
    $this->spider->registerObserver($one_more_observer);

    $uri = new lmbUri('http://some.host/whatever.html');

    $this->uri_filter->expectOnce('canPass');
    $this->uri_filter->setReturnValue('canPass', true, array($uri));
    $this->normalizer->expectOnce('process');

    $this->reader->expectOnce('open', array($uri));
    $this->reader->setReturnValue('getContent', $content = 'whatever');
    $this->reader->setReturnValue('getContentType', $content_type = 'whatever');

    $this->content_type_filter->expectOnce('canPass', array($content_type));
    $this->content_type_filter->setReturnValue('canPass', true, array($content_type));

    $this->extractor->expectOnce('extract', array($content));
    $this->extractor->setReturnValue('extract', array(), array($content));

    $this->observer->expectOnce('notify', array(new IsAExpectation('MockUriContentReader')));
    $one_more_observer->expectOnce('notify', array(new IsAExpectation('MockUriContentReader')));

    $this->spider->crawl($uri);

    $one_more_observer->tally();
  }

  function testCrawlCacheHitComplexUrl()
  {
    $this->observer->expectCallCount('notify', 2);

    $uri = new lmbUri('http://example.com/index.html');
    $uri_normalized_by_spider = new lmbUri('http://example.com/level1/page1.html');

    $this->uri_filter->expectCallCount('canPass', 2);
    $this->uri_filter->setReturnValueAt(0, 'canPass', true, array($uri));
    $this->uri_filter->setReturnValueAt(1, 'canPass', true, array($uri_normalized_by_spider));

    $this->normalizer->expectCallCount('process', 5);
    $this->normalizer->expectArgumentsAt(0, 'process', array($uri));
    $this->normalizer->expectArgumentsAt(1, 'process', array($uri));
    $this->normalizer->expectArgumentsAt(2, 'process', array($uri_normalized_by_spider));
    $this->normalizer->expectArgumentsAt(3, 'process', array($uri));
    $this->normalizer->expectArgumentsAt(4, 'process', array($uri_normalized_by_spider));

    $this->reader->expectCallCount('open', 2);
    $this->reader->expectArgumentsAt(0, 'open', array($uri));
    $this->reader->expectArgumentsAt(1, 'open', array($uri_normalized_by_spider));

    $this->reader->expectCallCount('getContent', 2);
    $this->reader->setReturnValueAt(0, 'getContent', $content1 = 'whatever1');
    $this->reader->setReturnValueAt(0, 'getContentType', $content_type1 = 'type1');
    $this->reader->setReturnValueAt(1, 'getContent', $content2 = 'whatever2');
    $this->reader->setReturnValueAt(1, 'getContentType', $content_type2 = 'type2');

    $this->content_type_filter->expectCallCount('canPass', 2);
    $this->content_type_filter->setReturnValueAt(0 ,'canPass', true, array($content_type1));
    $this->content_type_filter->setReturnValueAt(1 ,'canPass', true, array($content_type2));

    $links1 = array(new lmbUri('index.html'), new lmbUri('level1/page1.html#anchor'));
    $links2 = array(new lmbUri('../index.html'), new lmbUri('page1.html'));

    $this->extractor->expectCallCount('extract', 2);
    $this->extractor->setReturnValue('extract', $links1, array($content1));
    $this->extractor->setReturnValue('extract', $links2, array($content2));
    $this->spider->crawl($uri);
  }
}


