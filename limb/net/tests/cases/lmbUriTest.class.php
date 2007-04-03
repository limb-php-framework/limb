<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUriTest.class.php 5181 2007-03-05 09:52:41Z tony $
 * @package    net
 */
lmb_require('limb/net/src/lmbUri.class.php');

class lmbUriTest extends UnitTestCase
{
  var $uri;
  function setUp()
  {
    $this->uri = new lmbUri();
  }

  function testParse()
  {
    $url = 'http://admin:test@localhost:81/test.php/test?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual($this->uri->getProtocol(), 'http');
    $this->assertEqual($this->uri->getHost(), 'localhost');
    $this->assertEqual($this->uri->getUser(), 'admin');
    $this->assertEqual($this->uri->getPassword(), 'test');
    $this->assertEqual($this->uri->getPort(), '81');
    $this->assertEqual($this->uri->getAnchor(), '23');

    $this->assertEqual($this->uri->getQueryItem('foo'), 'bar');
    $this->assertEqual($this->uri->countQueryItems(), 1);

    $this->assertEqual($this->uri->getPath(), '/test.php/test');
    $this->assertEqual($this->uri->countPath(), 3);
    $this->assertEqual($this->uri->getPathElements(), array('', 'test.php', 'test'));
    $this->assertEqual($this->uri->getPathElement(0), '');
    $this->assertEqual($this->uri->getPathElement(1), 'test.php');
    $this->assertEqual($this->uri->getPathElement(2), 'test');
  }

  function testToStringDefault()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual($this->uri->toString(), $url);
  }

  function testToStringQueryItemsSort()
  {
    $url = 'http://localhost/test.php?b=1&a=2&c[1]=456';
    $expected_url = 'http://localhost/test.php?a=2&b=1&c[1]=456';

    $this->uri->parse($url);

    $this->assertEqual($this->uri->toString(), $expected_url);
  }

  function testToStringNoProtocol()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->toString(array('user', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'admin:test@localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoUser()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->toString(array('protocol', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'http://localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoPassword()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->toString(array('protocol', 'user', 'host', 'port', 'path', 'query', 'anchor')),
      'http://admin@localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoHost()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->toString(array('protocol', 'user', 'password', 'port', 'path', 'query', 'anchor')),
      '/test.php?foo=bar#23'
    );
  }

  function testToStringNoPath()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'query', 'anchor')),
      'http://admin:test@localhost:81?foo=bar#23'
    );
  }

  function testToStringNoQuery()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'anchor')),
      'http://admin:test@localhost:81/test.php#23'
    );
  }

  function testToStringNoAnchor()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path')),
      'http://admin:test@localhost:81/test.php'
    );
  }

  function testSetQueryString()
  {
    $url = 'http://localhost';

    $this->uri->parse($url);

    $this->uri->setQueryString('foo=bar&bar=foo');

    $this->assertEqual($this->uri->countQueryItems(), 2);
    $this->assertEqual($this->uri->getQueryItem('foo'), 'bar');
    $this->assertEqual($this->uri->getQueryItem('bar'), 'foo');
  }

  function testSetQueryString2()
  {
    $url = 'http://localhost';

    $this->uri->parse($url);
    $this->uri->setQueryString('foo[i1]=1&foo[i2]=2');

    $this->assertEqual($this->uri->countQueryItems(), 1);
    $this->assertEqual($this->uri->getQueryItem('foo'), array('i1' => '1', 'i2' => '2'));
  }

  function testNormalizePath()
  {
    $this->uri->parse('/foo/bar/../boo.php');
    $this->uri->normalizePath();
    $this->assertEqual($this->uri, new lmbUri('/foo/boo.php'));

    $this->uri->parse('/foo/bar/../../boo.php');
    $this->uri->normalizePath();
    $this->assertEqual($this->uri, new lmbUri('/boo.php'));

    $this->uri->parse('/foo/bar/../boo.php');
    $this->uri->normalizePath();
    $this->assertEqual($this->uri, new lmbUri('/foo/boo.php'));

    $this->uri->parse('/foo//bar//boo.php');
    $this->uri->normalizePath();
    $this->assertEqual($this->uri, new lmbUri('/foo/bar/boo.php'));

    $this->uri->parse('/foo//bar///boo.php');
    $this->uri->normalizePath();
    $this->assertEqual($this->uri, $uri = new lmbUri('/foo/bar/boo.php'));
    $this->assertEqual($this->uri->getPath(), $uri->getPath());
  }

  function testAddQueryItem()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->addQueryItem('bar', 'foo');
    $this->assertEqual($this->uri->getQueryString(), 'bar=foo&foo=bar');
  }

  function testAddQueryItem2()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->addQueryItem('foo', 'foo');
    $this->assertEqual($this->uri->getQueryString(), 'foo=foo');
  }

  function testAddQueryItem3()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->addQueryItem('foo', array('i1' => 'bar'));
    $this->uri->addQueryItem('bar', 1);
    $this->assertEqual($this->uri->getQueryString(), 'bar=1&foo[i1]=bar');
  }

  function testAddQueryItem4()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->addQueryItem('foo', array('i1' => array('i2' => 'bar')));
    $this->uri->addQueryItem('bar', 1);
    $this->assertEqual($this->uri->getQueryString(), 'bar=1&foo[i1][i2]=bar');
  }

  function testAddQueryItemUrlencode()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->addQueryItem('foo', ' foo ');
    $this->assertEqual($this->uri->getQueryString(), 'foo=+foo+');
  }

  function testAddQueryItemUrlencode2()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->addQueryItem('foo', array('i1' => ' bar '));
    $this->assertEqual($this->uri->getQueryString(), 'foo[i1]=+bar+');
  }

  function testCompareQueryEqual()
  {
    $url = 'http://admin:test@localhost2:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareQueryNotEqual()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar2#23')
     ));
  }

  function testCompareQueryNotEqual2()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo#23')
     ));
  }

  function testCompareIdentical()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?foo=bar#23')));
  }

  function testCompareEqual()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?foo=bar&bar=foo#23')));
  }

  function testCompareEqual2()
  {
    $url = 'http://admin:test@localhost:81?';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new lmbUri('http://admin:test@localhost:81')
     ));
  }

  function testCompareNotEqualSchema()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new lmbUri('https://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualUser()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new lmbUri('http://admin1:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPassword()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new lmbUri('http://admin:test1@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualHost()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new lmbUri('http://admin:test@localhost1:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPort()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new lmbUri('http://admin:test@localhost/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPath()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPath2()
  {
    $url = 'http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php/test1?bar=foo&foo=bar#23')
     ));
  }

  function testCompareAnchorDoesntMatter()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#32')
     ));
  }

  function testComparePathEqual()
  {
    $url = 'http://localhost/test.php/test';

    $this->uri->parse($url);

    $this->assertEqual(0,
      $this->uri->comparePath(
        new lmbUri('http://localhost2/test.php/test')
      )
    );
  }

  function testComparePathContains()
  {
    $url = 'http://localhost/test.php/test';

    $this->uri->parse($url);

    $this->assertEqual(1,
      $this->uri->comparePath(
        new lmbUri('http://localhost2/test.php')
      )
    );
  }

  function testComparePathIsContained()
  {
    $url = 'http://localhost/test.php/test';

    $this->uri->parse($url);

    $this->assertEqual(-1,
      $this->uri->comparePath(
        new lmbUri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function testComparePathNotEqual()
  {
    $url = 'http://localhost/test.php/test/test1';

    $this->uri->parse($url);

    $this->assertIdentical(false,
      $this->uri->comparePath(
        new lmbUri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function testRemoveQueryItem()
  {
    $url = 'http://localhost/test.php?foo=bar&bar=foo';

    $this->uri->parse($url);

    $this->uri->removeQueryItem('bar');

    $this->assertEqual('foo=bar', $this->uri->getQueryString());
    $this->assertEqual('http://localhost/test.php?foo=bar', $this->uri->toString());
  }

  function testRemoveQueryItems()
  {
    $url = 'http://localhost/test.php?foo=bar&bar=foo';

    $this->uri->parse($url);

    $this->uri->removeQueryItems();

    $this->assertEqual('', $this->uri->getQueryString());
    $this->assertEqual('http://localhost/test.php', $this->uri->toString());
  }

  function testIsAbsolute()
  {
    $url = '/test.php';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->isAbsolute());
  }

  function testIsAbsoluteNoPath()
  {
    $url = 'http://somedomain.com';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->isAbsolute());
  }

  function testIsRelative()
  {
    $url = '../../test.php';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->isRelative());
  }

  function testSetPath()
  {
    $this->uri->setPath('/index.html');

    $uri = new lmbUri('http://dot.com/index.html');

    $this->assertEqual($uri->getPathElements(), $this->uri->getPathElements());
  }

  function testGetPathToLevel()
  {
    $uri = new lmbUri('/path/to/level');

    $this->assertEqual($uri->getPathToLevel(1), '/path');
    $this->assertEqual($uri->getPathToLevel(2), '/path/to');
    $this->assertEqual($uri->getPathToLevel(3), '/path/to/level');
    $this->assertEqual($uri->getPathToLevel(4), '');
  }

  function testGetPathFromLevel()
  {
    $uri = new lmbUri('/path/to/level');

    $this->assertEqual($uri->getPathFromLevel(0), '/path/to/level');
    $this->assertEqual($uri->getPathFromLevel(1), '/path/to/level');
    $this->assertEqual($uri->getPathFromLevel(2), '/to/level');
    $this->assertEqual($uri->getPathFromLevel(3), '/level');
    $this->assertEqual($uri->getPathFromLevel(4), '/');
  }

  function testUrlencodedPartsOfQueryAreDecoded()
  {
    $uri = new lmbUri('index.html?wow=' . urlencode('what a nice weather'));
    $this->assertEqual($uri->getQueryItem('wow'), 'what a nice weather');
  }
}

?>