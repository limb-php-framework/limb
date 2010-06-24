<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/net/src/lmbUri.class.php');

class lmbUriTest extends UnitTestCase
{
  function testCreate()
  {
    $str = 'http://admin:test@localhost:81/test.php/test?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->getProtocol(), 'http');
    $this->assertEqual($uri->getHost(), 'localhost');
    $this->assertEqual($uri->getUser(), 'admin');
    $this->assertEqual($uri->getPassword(), 'test');
    $this->assertEqual($uri->getPort(), '81');
    $this->assertEqual($uri->getAnchor(), '23');

    $this->assertEqual($uri->getQueryItem('foo'), 'bar');
    $this->assertEqual($uri->countQueryItems(), 1);

    $this->assertEqual($uri->getPath(), '/test.php/test');
    $this->assertEqual($uri->countPath(), 3);
    $this->assertEqual($uri->getPathElements(), array('', 'test.php', 'test'));
    $this->assertEqual($uri->getPathElement(0), '');
    $this->assertEqual($uri->getPathElement(1), 'test.php');
    $this->assertEqual($uri->getPathElement(2), 'test');
  }

  function testCreate_FileProtocolWithoutHost_OnUnix()
  {
    $str = 'file:///dir';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->getProtocol(), 'file');
    $this->assertEqual($uri->getHost(), '');

    $this->assertEqual($uri->getPath(), '/dir');
  }

  function testCreate_FileProtocolWithoutHost_OnWindows()
  {
    $str = 'file://c:\dir\another_dir\\';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->getProtocol(), 'file');
    $this->assertEqual($uri->getHost(), '');

    $this->assertEqual($uri->getPath(), 'c:\dir\another_dir\\');

    $str = 'file://c:/dir/another_dir';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->getProtocol(), 'file');
    $this->assertEqual($uri->getHost(), '');

    $this->assertEqual($uri->getPath(), 'c:/dir/another_dir');

    $str = 'file://c:\dir/another_dir';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->getProtocol(), 'file');
    $this->assertEqual($uri->getHost(), '');

    $this->assertEqual($uri->getPath(), 'c:\dir/another_dir');
  }

  function testCreate_FileProtocolWithHost()
  {
    $str = 'file://user:pass@localhost/dir/file';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->getProtocol(), 'file');
    $this->assertEqual($uri->getUser(), 'user');
    $this->assertEqual($uri->getPassword(), 'pass');
    $this->assertEqual($uri->getHost(), 'localhost');
    $this->assertEqual($uri->getPath(), '/dir/file');

    $str = 'file://user:pass@localhost/c:\dir\file';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->getProtocol(), 'file');
    $this->assertEqual($uri->getUser(), 'user');
    $this->assertEqual($uri->getPassword(), 'pass');
    $this->assertEqual($uri->getHost(), 'localhost');
    // should it be just c:\dir\file ???
    $this->assertEqual($uri->getPath(), '/c:\dir\file');
  }

  function testInvalidUriThrowsException()
  {
    try
    {
      $uri = new lmbUri('http:///');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testToStringDefault()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->toString(), $str);
  }

  function testToStringQueryItemsSort()
  {
    $str = 'http://localhost/test.php?b=1&a=2&c[1]=456';
    $expected_url = 'http://localhost/test.php?a=2&b=1&c[1]=456';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->toString(), $expected_url);
  }

  function testToStringNoProtocol()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual(
      $uri->toString(array('user', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'admin:test@localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoUser()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual(
      $uri->toString(array('protocol', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'http://localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoPassword()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual(
      $uri->toString(array('protocol', 'user', 'host', 'port', 'path', 'query', 'anchor')),
      'http://admin@localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoHost()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual(
      $uri->toString(array('protocol', 'user', 'password', 'port', 'path', 'query', 'anchor')),
      '/test.php?foo=bar#23'
    );
  }

  function testToStringNoPath()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual(
      $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'query', 'anchor')),
      'http://admin:test@localhost:81?foo=bar#23'
    );
  }

  function testToStringNoQuery()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual(
      $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'anchor')),
      'http://admin:test@localhost:81/test.php#23'
    );
  }

  function testToStringNoAnchor()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEqual(
      $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path')),
      'http://admin:test@localhost:81/test.php'
    );
  }

  function testSetQueryString()
  {
    $str = 'http://localhost';

    $uri = new lmbUri($str);

    $uri->setQueryString('foo=bar&bar=foo');

    $this->assertEqual($uri->countQueryItems(), 2);
    $this->assertEqual($uri->getQueryItem('foo'), 'bar');
    $this->assertEqual($uri->getQueryItem('bar'), 'foo');
  }

  function testSetQueryString2()
  {
    $str = 'http://localhost';

    $uri = new lmbUri($str);
    $uri->setQueryString('foo[i1]=1&foo[i2]=2');

    $this->assertEqual($uri->countQueryItems(), 1);
    $this->assertEqual($uri->getQueryItem('foo'), array('i1' => '1', 'i2' => '2'));
  }

  function testNormalizePath()
  {
    $uri = new lmbUri('/foo/bar/../boo.php');
    $uri->normalizePath();
    $this->assertEqual($uri, new lmbUri('/foo/boo.php'));

    $uri->reset('/foo/bar/../../boo.php');
    $uri->normalizePath();
    $this->assertEqual($uri, new lmbUri('/boo.php'));

    $uri->reset('/foo/bar/../boo.php');
    $uri->normalizePath();
    $this->assertEqual($uri, new lmbUri('/foo/boo.php'));

    $uri->reset('/foo//bar//boo.php');
    $uri->normalizePath();
    $this->assertEqual($uri, new lmbUri('/foo/bar/boo.php'));

    $uri->reset('/foo//bar///boo.php');
    $uri->normalizePath();
    $this->assertEqual($uri, $uri = new lmbUri('/foo/bar/boo.php'));
    $this->assertEqual($uri->getPath(), $uri->getPath());
  }

  function testAddQueryItem()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $uri->addQueryItem('bar', 'foo');
    $this->assertEqual($uri->getQueryString(), 'bar=foo&foo=bar');
  }

  function testAddQueryItem2()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $uri->addQueryItem('foo', 'foo');
    $this->assertEqual($uri->getQueryString(), 'foo=foo');
  }

  function testAddQueryItem3()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $uri->addQueryItem('foo', array('i1' => 'bar'));
    $uri->addQueryItem('bar', 1);
    $this->assertEqual($uri->getQueryString(), 'bar=1&foo[i1]=bar');
  }

  function testAddQueryItem4()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $uri->addQueryItem('foo', array('i1' => array('i2' => 'bar')));
    $uri->addQueryItem('bar', 1);
    $this->assertEqual($uri->getQueryString(), 'bar=1&foo[i1][i2]=bar');
  }

  function testAddQueryItemUrlencode()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $uri->addQueryItem('foo', ' foo ');
    $this->assertEqual($uri->getQueryString(), 'foo=+foo+');
  }

  function testAddQueryItemUrlencode2()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $uri->addQueryItem('foo', array('i1' => ' bar '));
    $this->assertEqual($uri->getQueryString(), 'foo[i1]=+bar+');
  }

  function testCompareQueryEqual()
  {
    $str = 'http://admin:test@localhost2:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareQueryNotEqual()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar2#23')
     ));
  }

  function testCompareQueryNotEqual2()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo#23')
     ));
  }

  function testCompareIdentical()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?foo=bar#23')));
  }

  function testCompareEqual()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?foo=bar&bar=foo#23')));
  }

  function testCompareEqual2()
  {
    $str = 'http://admin:test@localhost:81?';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81')
     ));
  }

  function testCompareNotEqualSchema()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('https://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualUser()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin1:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPassword()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test1@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualHost()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost1:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPort()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPath()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPath2()
  {
    $str = 'http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php/test1?bar=foo&foo=bar#23')
     ));
  }

  function testCompareAnchorDoesntMatter()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#32')
     ));
  }

  function testComparePathEqual()
  {
    $str = 'http://localhost/test.php/test';

    $uri = new lmbUri($str);

    $this->assertEqual(0,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php/test')
      )
    );
  }

  function testComparePathContains()
  {
    $str = 'http://localhost/test.php/test';

    $uri = new lmbUri($str);

    $this->assertEqual(1,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php')
      )
    );
  }

  function testComparePathIsContained()
  {
    $str = 'http://localhost/test.php/test';

    $uri = new lmbUri($str);

    $this->assertEqual(-1,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function testComparePathNotEqual()
  {
    $str = 'http://localhost/test.php/test/test1';

    $uri = new lmbUri($str);

    $this->assertIdentical(false,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function testRemoveQueryItem()
  {
    $str = 'http://localhost/test.php?foo=bar&bar=foo';

    $uri = new lmbUri($str);

    $uri->removeQueryItem('bar');

    $this->assertEqual('foo=bar', $uri->getQueryString());
    $this->assertEqual('http://localhost/test.php?foo=bar', $uri->toString());
  }

  function testRemoveQueryItems()
  {
    $str = 'http://localhost/test.php?foo=bar&bar=foo';

    $uri = new lmbUri($str);

    $uri->removeQueryItems();

    $this->assertEqual('', $uri->getQueryString());
    $this->assertEqual('http://localhost/test.php', $uri->toString());
  }

  function testIsAbsolute()
  {
    $str = '/test.php';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->isAbsolute());
  }

  function testIsAbsoluteNoPath()
  {
    $str = 'http://somedomain.com';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->isAbsolute());
  }

  function testIsRelative()
  {
    $str = '../../test.php';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->isRelative());
  }

  function testSetPath()
  {
    $uri1 = new lmbUri('/index.html');
    $uri2 = new lmbUri('http://dot.com/index.html');

    $this->assertEqual($uri1->getPathElements(), $uri2->getPathElements());
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

  function testToString_IfAttributeNoValidStringURL()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=' . urlencode('10&b=11') . '#23';

    $uri = new lmbUri($str);

    $this->assertEqual($uri->toString(), $str);
  }

  function testUrlDecode()
  {
    $test_value = '+text';
    $uri = new lmbUri('/index.html?var=' . urlencode($test_value));

    $q_items = $uri->getQueryItems();
    $this->assertEqual($q_items['var'], $test_value);
  }
}


