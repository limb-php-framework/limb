<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/web_spider/src/lmbUriExtractor.class.php');

class lmbUriExtractorTest extends UnitTestCase
{
  var $extractor;

  function setUp()
  {
    $this->extractor = new lmbUriExtractor();
  }

  function testFindLinks()
  {
    $content = <<< EOD
<html>
<head>
</head>
<body>
<a href="http://test.com">""  - link</a>

<a href='http://test.com'>'' - link</a>

<a href='http://test2.com?wow=1&bar=4'>'' - link with query</a>

<a href=http://test2.com>no quotes link</a>

<a href='/root/news/3' class='title-site2'>link with attributes in atag</a>

<a href='/root/news/4'>

multiline
 link
 </a>

</body>
</html>
EOD;

    $links = $this->extractor->extract($content);
    $this->assertEqual(sizeof($links), 6);

    $this->assertEqual($links[0], new lmbUri('http://test.com'));
    $this->assertEqual($links[1], new lmbUri('http://test.com'));
    $this->assertEqual($links[2], new lmbUri('http://test2.com?wow=1&bar=4'));
    $this->assertEqual($links[3], new lmbUri('http://test2.com'));
    $this->assertEqual($links[4], new lmbUri('/root/news/3'));
    $this->assertEqual($links[5], new lmbUri('/root/news/4'));
  }
}


