<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInnerUriNormalizerObserverTest.class.php 5014 2007-02-08 15:38:18Z pachanga $
 * @package    web_spider
 */
lmb_require('limb/web_spider/src/lmbInnerUriNormalizerObserver.class.php');
lmb_require('limb/web_spider/src/lmbUriContentReader.class.php');

Mock :: generate('lmbUriContentReader', 'MockUriContentReader');

class lmbInnerUriNormalizerObserverTest extends UnitTestCase
{
  var $observer;
  var $reader;

  function setUp()
  {
    $this->reader = new MockUriContentReader($this);
  }

  function tearDown()
  {
    $this->reader->tally();
  }

  function testNotifyInnerUrl()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('http://test.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEqual($uri->toString(), '/page.html');
  }

  function testNotifyOtherProtocol()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('ftp://test.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEqual($uri->toString(), 'ftp://test.com/page.html');
  }

  function testNotifyOtherPort()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com:22'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('http://test.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEqual($uri->toString(), 'http://test.com/page.html');
  }

  function testNotifyExternalUrl()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('http://test2.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEqual($uri->toString(), 'http://test2.com/page.html');
  }

}

?>
