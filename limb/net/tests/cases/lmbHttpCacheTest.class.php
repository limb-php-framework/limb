<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/net/src/lmbHttpResponse.class.php');
lmb_require('limb/net/src/lmbHttpCache.class.php');

Mock :: generate('lmbHttpResponse', 'MockHttpResponse');

class lmbHttpCacheTest extends UnitTestCase
{
  var $response;
  var $cache;
  var $server_vars;

  function setUp()
  {
    $this->response = new MockHttpResponse();
    $this->cache = new lmbHttpCache();
    $this->server_vars = $_SERVER;
  }

  function tearDown()
  {
    $_SERVER = $this->server_vars;
  }

  function testSetCacheSettings()
  {
    $this->cache->setLastModifiedTime($time = time());
    $this->assertEqual($this->cache->getLastModifiedTime(), $time);
    $this->assertEqual($this->cache->formatLastModifiedTime(), gmdate('D, d M Y H:i:s \G\M\T', $time));

    $this->cache->setEtag($etag = md5(time()));
    $this->assertEqual($this->cache->getEtag(), $etag);

    $this->cache->setCacheTime(10);
    $this->assertEqual($this->cache->getCacheTime(), 10);

    $this->cache->setCacheType('public');
    $this->assertEqual($this->cache->getCacheType(), 'public');
  }

  function testGetDefaultEtag1()
  {
    $script = 'test';
    $query = 'query';

    $_SERVER['QUERY_STRING'] = $query;
    $_SERVER['SCRIPT_FILENAME'] = $script;

    $this->cache->setLastModifiedTime($time = time());
    $etag = $this->cache->getEtag();

    $this->assertEqual($etag, '"' . md5($script . '?' . $query . '#' . $time ) . '"');
  }

  function testGetDefaultEtag2()
  {
    $script = 'test';
    $query = 'query';

    $_SERVER['QUERY_STRING'] = $query;
    unset($_SERVER['SCRIPT_FILENAME']);
    $_SERVER['PATH_TRANSLATED'] = $script;

    $this->cache->setLastModifiedTime($time = time());
    $etag = $this->cache->getEtag();

    $this->assertEqual($etag, '"' . md5($script . '?' . $query . '#' . $time ) . '"');
  }

  function testGetDefaultEtag3()
  {
    $script = 'test';

    unset($_SERVER['QUERY_STRING']);
    $_SERVER['SCRIPT_FILENAME'] = $script;

    $this->cache->setLastModifiedTime($time = time());
    $etag = $this->cache->getEtag();

    $this->assertEqual($etag, '"' . md5($script . '#' . $time ) . '"');
  }

  function testIs412False()
  {
    $this->assertFalse($this->cache->is412());
  }

  function testIs412FalsePartOfEtag()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'big_etag';

    $this->cache->setEtag('etag');

    $this->assertFalse($this->cache->is412());
  }

  function testIs412FalseAsteric()
  {
    $_SERVER['HTTP_IF_MATCH'] = '*';

    $this->cache->setEtag('etag');

    $this->assertFalse($this->cache->is412());
  }

  function testIs412Etag()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'wrong';

    $this->cache->setEtag('etag');

    $this->assertTrue($this->cache->is412());
  }

  function testIs412UnmodifiedSince()
  {
    $this->cache->setLastModifiedTime($time = time());

    $_SERVER['HTTP_IF_UNMODIFIED_SINCE'] = gmdate('D, d M Y H:i:s \G\M\T', $time - 100);

    $this->assertTrue($this->cache->is412());
  }

  function testIs304False()
  {
    $this->assertFalse($this->cache->is304());
  }

  function testIs304LastModifiedTime()
  {
    $this->cache->setLastModifiedTime($time = time());

    $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $this->cache->formatLastModifiedTime();

    $this->assertTrue($this->cache->is304());
  }

  function testIs304Etag()
  {
    $etag = 'etag';

    unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $_SERVER['HTTP_IF_NONE_MATCH'] = $etag;

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setEtag($etag);

    $this->assertTrue($this->cache->is304());
  }

  function testIs304EtagAsteric()
  {
    $etag = 'etag';

    unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $_SERVER['HTTP_IF_NONE_MATCH'] = '*';

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setEtag($etag);

    $this->assertTrue($this->cache->is304());
  }

  function testCheckAndWrite412()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'wrong';

    $this->cache->setEtag('etag');

    $this->response->expectCallCount('addHeader', 3);
    $this->response->expectArgumentsAt(0, 'addHeader', array('HTTP/1.1 412 Precondition Failed'));
    $this->response->expectArgumentsAt(1, 'addHeader', array('Cache-Control: protected, max-age=0, must-revalidate'));
    $this->response->expectArgumentsAt(2, 'addHeader', array('Content-Type: text/plain'));

    $this->response->expectOnce('write', array(new WantedPatternExpectation("~^HTTP/1.1 Error 412~")));

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWrite304()
  {
    $_SERVER['HTTP_IF_NONE_MATCH'] = 'etag';

    $this->cache->setEtag('etag');

    $this->response->expectCallCount('addHeader', 6);
    $this->response->expectArgumentsAt(0, 'addHeader', array('HTTP/1.0 304 Not Modified'));
    $this->response->expectArgumentsAt(1, 'addHeader', array('Etag: etag'));
    $this->response->expectArgumentsAt(2, 'addHeader', array('Pragma: '));
    $this->response->expectArgumentsAt(3, 'addHeader', array('Cache-Control: '));
    $this->response->expectArgumentsAt(4, 'addHeader', array('Last-Modified: '));
    $this->response->expectArgumentsAt(5, 'addHeader', array('Expires: '));

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteFalseNotHead()
  {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->assertFalse($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteNoCacheTime()
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';

    $this->cache->setLastModifiedTime($time = time());

    $this->response->expectCallCount('addHeader', 5);
    $this->response->expectArgumentsAt(0, 'addHeader', array('Cache-Control: protected, must-revalidate, max-age=0'));
    $this->response->expectArgumentsAt(1, 'addHeader', array('Last-Modified: ' . $this->cache->formatLastModifiedTime()));
    $this->response->expectArgumentsAt(2, 'addHeader', array('Etag: ' . $this->cache->getEtag()));
    $this->response->expectArgumentsAt(3, 'addHeader', array('Pragma: '));
    $this->response->expectArgumentsAt(4, 'addHeader', array('Expires: '));

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteWithCacheTime()
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setCacheTime(100);

    $this->response->expectCallCount('addHeader', 5);
    $this->response->expectArgumentsAt(0, 'addHeader', array('Cache-Control: protected, max-age=100'));
    $this->response->expectArgumentsAt(1, 'addHeader', array('Last-Modified: ' . $this->cache->formatLastModifiedTime()));
    $this->response->expectArgumentsAt(2, 'addHeader', array('Etag: ' . $this->cache->getEtag()));
    $this->response->expectArgumentsAt(3, 'addHeader', array('Pragma: '));
    $this->response->expectArgumentsAt(4, 'addHeader', array('Expires: '));

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteWithPrivacy()
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setCacheTime(100);
    $this->cache->setCacheType(lmbHttpCache::TYPE_PUBLIC);

    $this->response->expectCallCount('addHeader', 5);
    $this->response->expectArgumentsAt(0, 'addHeader', array('Cache-Control: public, max-age=100'));
    $this->response->expectArgumentsAt(1, 'addHeader', array('Last-Modified: ' . $this->cache->formatLastModifiedTime()));
    $this->response->expectArgumentsAt(2, 'addHeader', array('Etag: ' . $this->cache->getEtag()));
    $this->response->expectArgumentsAt(3, 'addHeader', array('Pragma: '));
    $this->response->expectArgumentsAt(4, 'addHeader', array('Expires: '));

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }
}

