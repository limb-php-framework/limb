<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUriNormalizerTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/web_spider/src/lmbUriNormalizer.class.php');


class lmbUriNormalizerTest extends UnitTestCase
{
  var $normalizer;

  function setUp()
  {
    $this->normalizer = new lmbUriNormalizer();
  }

  function testNormalizeStripAnchor()
  {
    $links = array(new lmbUri('index.html?a=1&b=2#test'));

    $this->normalizer->process($links[0]);
    $this->assertEqual($links[0], new lmbUri('index.html?a=1&b=2'));
  }

  function testNormalizeStripQuery()
  {
    $links = array(new lmbUri('index.html?a=1&b=2'),
                   new lmbUri('http://test.com/page1.html?whatever'),
                   new lmbUri('http://test.com/page2.html?PHPSESSID=id&a=1'));

    $this->normalizer->stripQueryItem('PHPSESSID');
    $this->normalizer->stripQueryItem('whatever');

    $this->normalizer->process($links[0]);
    $this->assertEqual($links[0], new lmbUri('index.html?a=1&b=2'));

    $this->normalizer->process($links[1]);
    $this->assertEqual($links[1], new lmbUri('http://test.com/page1.html'));

    $this->normalizer->process($links[2]);
    $this->assertEqual($links[2], new lmbUri('http://test.com/page2.html?a=1'));
  }
}

?>
