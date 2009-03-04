<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/web_spider/src/lmbUriContentReader.class.php');

Mock :: generate('lmbUri', 'MockUri');

class lmbUriContentReaderTest extends UnitTestCase
{
  function testOpen()
  {
    $uri = new MockUri();
    $reader = new lmbUriContentReader();
    $uri->expectOnce('toString');
    $uri->setReturnValue('toString', dirname(__FILE__) . '/../html/index.html');
    $reader->open($uri);
    $this->assertFalse($reader->getContentType()); // since opening a plain text file not html over http
    $this->assertEqual($reader->getContent(),
                       file_get_contents(dirname(__FILE__) . '/../html/index.html'));
  }

  function TODO_testGetLazyContent()
  {
  }
}


