<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/web_agent/src/lmbWebAgentHeaders.class.php');

/**
 * @package web_agent
 * @version $Id: lmbWebAgentHeadersTest.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebAgentHeadersTest extends UnitTestCase {

  protected function _getHeaders()
  {
    return new lmbWebAgentHeaders(array('GET / HTTP/1.1' => null, 'Host' => 'test.ru'));
  }

  function testGetSet()
  {
    $headers = $this->_getHeaders();

    $this->assertEqual($headers->get('host'), 'test.ru');

    $headers->set('User-Agent', 'TestAgent');
    $this->assertEqual($headers->get('user-agent'), 'TestAgent');
  }

  function testSetRaw()
  {
    $headers = $this->_getHeaders();

    $headers->setRaw('Content-MD5', 'zxc');
    $this->assertEqual($headers->get('Content-MD5'), 'zxc');
  }

  function testGetFirst()
  {
    $headers = $this->_getHeaders();

    $this->assertEqual($headers->getFirst(), 'GET / HTTP/1.1');
  }

  function testHasHeader()
  {
    $headers = $this->_getHeaders();

    $this->assertTrue($headers->has('host'));
    $this->assertFalse($headers->has('user-agent'));
  }

  function testClean()
  {
    $headers = $this->_getHeaders();

    $this->assertTrue($headers->has('host'));
    $headers->clean();
    $this->assertFalse($headers->has('host'));
  }

  function testCountHeaders()
  {
    $headers = $this->_getHeaders();

    $this->assertEqual($headers->countHeaders('host'), 1);
    $this->assertEqual($headers->countHeaders('user-agent'), 0);

    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');
    $this->assertEqual($headers->countHeaders('set-cookie'), 2);
  }

  function testGetByNum()
  {
    $headers = $this->_getHeaders();

    $this->assertEqual($headers->get('host', 0), 'test.ru');
    $this->assertNull($headers->get('host', 1));

    $this->assertNull($headers->get('user-agent', 0));

    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');
    $this->assertEqual($headers->get('set-cookie', 0), 'sid=1');
    $this->assertEqual($headers->get('set-cookie', 1), 'sid2=2');
    $this->assertNull($headers->get('set-cookie', 3));
  }

  function testParse()
  {
  	$headers = new lmbWebAgentHeaders();

    $this->assertTrue($headers->parse("GET / HTTP/1.1\r\n"));
    $this->assertTrue($headers->parse('Host: test.ru'));
    $this->assertTrue($headers->parse('Set-Cookie: sid=1'));
    $this->assertTrue($headers->parse('Set-Cookie: sid2=2'));
    $this->assertFalse($headers->parse("\r\n"));

    $this->assertEqual($headers->getFirst(), 'GET / HTTP/1.1');
    $this->assertEqual($headers->get('host'), 'test.ru');
    $this->assertEqual($headers->get('set-cookie', 0), 'sid=1');
    $this->assertEqual($headers->get('set-cookie', 1), 'sid2=2');
  }

  function testExportHeader()
  {
    $headers = $this->_getHeaders();

    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');
    $this->assertEqual($headers->exportHeader('host'), 'Host: test.ru');
    $this->assertEqual($headers->exportHeader('set-cookie', 0), 'Set-Cookie: sid=1');
    $this->assertEqual($headers->exportHeader('set-cookie', 1), 'Set-Cookie: sid2=2');
    $this->assertNull($headers->exportHeader('set-cookie', 3));
  }

  function testExportHeaders()
  {
    $headers = $this->_getHeaders();
    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');

    $str = $headers->exportHeaders();
    $this->assertEqual($str, "GET / HTTP/1.1\r\nHost: test.ru\r\nSet-Cookie: sid=1\r\nSet-Cookie: sid2=2\r\n");
  }

  function testRemove()
  {
    $headers = $this->_getHeaders();
    $headers->set('User-Agent', 'TestAgent');

    $this->assertTrue($headers->has('host'));
    $headers->remove('host');
    $this->assertFalse($headers->has('host'));
    $this->assertEqual($headers->get('user-agent'), 'TestAgent');
  }

  function testCopyTo()
  {
    $headers = $this->_getHeaders();
    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');

    $headers_dest = new lmbWebAgentHeaders();
    $headers->copyTo($headers_dest);
    $str = $headers_dest->exportHeaders();
    $this->assertEqual($str, "GET / HTTP/1.1\r\nHost: test.ru\r\nSet-Cookie: sid=1\r\nSet-Cookie: sid2=2\r\n");
  }

}
