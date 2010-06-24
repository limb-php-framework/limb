<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/net/src/lmbHttpRedirectStrategy.class.php');

Mock :: generatePartial(
  'lmbHttpResponse',
  'SpecialMockResponse',
  array('_sendHeader',
        '_sendCookie',
        '_sendString',
        '_sendFile')
);

Mock::generate('lmbHttpRedirectStrategy', 'MockHttpRedirectStrategy');

class lmbHttpResponseTest extends UnitTestCase
{
  var $response;

  function setUp()
  {
    $this->response = new SpecialMockResponse();
  }

  function testIsEmpty()
  {
    $this->assertTrue($this->response->isEmpty());
  }

  function testIsEmptyHeadersSent()
  {
    $this->response->addHeader('test');
    $this->assertTrue($this->response->isEmpty());
  }

  function testNotEmptyRedirect()
  {
    $this->response->redirect("/to/some/place?t=1&amp;t=2");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmptyResponseString()
  {
    $this->response->write("<b>wow</b>");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmptyReadfile()
  {
    $this->response->readfile("/path/to/file");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmpty304Status()
  {
    $this->response->addHeader('HTTP/1.0 304 Not Modified');
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmpty412Status()
  {
    $this->response->addHeader('HTTP/1.1 412 Precondition Failed');
    $this->assertFalse($this->response->isEmpty());
  }

  function testHeadersNotSent()
  {
    $this->assertFalse($this->response->isHeadersSent());
  }

  function testFileNotSent()
  {
    $this->assertFalse($this->response->isFileSent());
  }

  function testFileSent()
  {
    $this->response->readfile('somefile');
    $this->assertTrue($this->response->isFileSent());
  }

  function testHeadersSent()
  {
    $this->response->addHeader("Location:to-some-place");
    $this->assertTrue($this->response->isHeadersSent());
  }

  function testRedirect()
  {
    $this->assertFalse($this->response->isRedirected());

    $this->response->redirect($path = 'some path');

    $this->assertTrue($this->response->isRedirected());
    $this->assertEqual($this->response->getRedirectedPath(), $path);
  }

  function testRedirectOnlyOnce()
  {
    $strategy = new MockHttpRedirectStrategy();

    $this->response->setRedirectStrategy($strategy);

    $this->assertFalse($this->response->isRedirected());

    $strategy->expectOnce('redirect');
    $this->response->redirect($path = 'some path');
    $this->response->redirect('some other path');

    $this->assertTrue($this->response->isRedirected());
    $this->assertEqual($this->response->getRedirectedPath(), $path);
  }

  function testSendHeadersOnCommit()
  {
    $this->response->addHeader("Location:to-some-place");
    $this->response->addHeader("Location:to-some-place2");

    $this->response->expectCallCount('_sendHeader', 2);
    $this->response->expectArgumentsAt(0, '_sendHeader', array("Location:to-some-place"));
    $this->response->expectArgumentsAt(1, '_sendHeader', array("Location:to-some-place2"));

    $this->response->commit();
  }

  function testWriteOnCommit()
  {
    $this->response->write("<b>wow</b>");
    $this->response->expectOnce('_sendString', array("<b>wow</b>"));
    $this->response->commit();
  }

  function testReadfileOnCommit()
  {
    $this->response->readfile("/path/to/file");
    $this->response->expectOnce('_sendFile', array("/path/to/file"));
    $this->response->commit();
  }

  function testSendCookiesOnCommit()
  {
    $this->response->setCookie($name1 = 'foo', $value1 = '1', $expire1 = 10, $path1 = '/', $domain1 = '.org', $secure1 = true);
    $this->response->setCookie($name2 = 'bar', $value2 = '2', $expire2 = 20, $path2 = '/path', $domain2 = 'net.org', $secure2 = false);

    $this->response->expectCallCount('_sendCookie', 2);
    $this->response->expectArgumentsAt(0, '_sendCookie', array(array('name' => $name1,
                                                                     'value' => $value1,
                                                                     'expire' => $expire1,
                                                                     'path' => $path1,
                                                                     'domain' => $domain1,
                                                                     'secure' => $secure1
                                                                     )));
    $this->response->expectArgumentsAt(1, '_sendCookie', array(array('name' => $name2,
                                                                     'value' => $value2,
                                                                     'expire' => $expire2,
                                                                     'path' => $path2,
                                                                     'domain' => $domain2,
                                                                     'secure' => $secure2
                                                                     )));
    $this->response->commit();
  }

  function testGetResponseDefaultStatus()
  {
    $this->assertEqual($this->response->getStatus(), 200);
  }

  function testGetResponseStatusHttp()
  {
    $this->response->addHeader('HTTP/1.0  304 ');
    $this->assertEqual($this->response->getStatus(), 304);

    $this->response->addHeader('HTTP/1.1  412');
    $this->assertEqual($this->response->getStatus(), 412);
  }

  function testGetUnknownDirective()
  {
    $this->assertFalse($this->response->getDirective('cache-control'));
  }

  function testGetDirective()
  {
    $this->response->addHeader('Cache-Control: protected, max-age=0, must-revalidate');
    $this->assertEqual($this->response->getDirective('cache-control'), 'protected, max-age=0, must-revalidate');

    $this->response->addHeader('Cache-Control :    protected, max-age=10  ');
    $this->assertEqual($this->response->getDirective('cache-control'), 'protected, max-age=10');
  }

  function testGetContentDefaultType()
  {
    $this->assertEqual($this->response->getContentType(), 'text/html');
  }

  function testGetContentType()
  {
    $this->response->addHeader('Content-Type: image/png');
    $this->assertEqual($this->response->getContentType(), 'image/png');

    $this->response->addHeader('Content-Type: application/rss+xml');
    $this->assertEqual($this->response->getContentType(), 'application/rss+xml');
  }

  function testGetContentTypeWithDelimiter()
  {
    $this->response->addHeader('Content-Type: text/html; charset=UTF-8');
    $this->assertEqual($this->response->getContentType(), 'text/html');
  }
}


