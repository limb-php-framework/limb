<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUriContentReaderTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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

?>
