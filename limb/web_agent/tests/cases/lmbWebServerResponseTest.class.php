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
lmb_require('limb/web_agent/src/lmbWebServerResponse.class.php');
lmb_require('limb/web_agent/src/lmbWebServerCookieCollection.class.php');

/**
 * Web server response
 *
 * @package web_agent
 * @version $Id: lmbWebServerResponseTest.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebServerResponseTest extends UnitTestCase {

  function testGetResponseParams()
  {
    $cookies = new lmbWebServerCookiesCollection();
    $headers = new lmbWebAgentHeaders(array('Location' => 'http://localhost'));
    $response = new lmbWebServerResponse('content', 200, 'text/html', 'utf-8', $cookies, $headers);

    $this->assertEqual($response->getContent(), 'content');
    $this->assertEqual($response->getStatus(), 200);
    $this->assertEqual($response->getMediaType(), 'text/html');
    $this->assertEqual($response->getCharset(), 'utf-8');
    $this->assertEqual($response->getHeaders()->get('location'), 'http://localhost');
    $this->assertFalse($response->getHeaders()->get('p3p'));
    $this->assertIdentical($response->getCookies(), $cookies);
  }

}
