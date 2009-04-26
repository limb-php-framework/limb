<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_spider/src/lmbUriFilter.class.php');

class lmbUriFilterTest extends UnitTestCase
{
  var $filter;

  function setUp()
  {
    $this->filter = new lmbUriFilter();
  }

  function testFilterAcceptedProtocols()
  {
    $links = array(new lmbUri('http://test1.com'),
                   new lmbUri('svn+ssh://test-broken.com'),
                   new lmbUri('https://test1.com'),
                   new lmbUri('ftp://test-broken.com'));

    $this->filter->allowHost('test1.com');
    $this->filter->allowPathRegex('~.*~');
    $this->filter->allowProtocol('http');
    $this->filter->allowProtocol('HTTPS');//protocols are lowercased

    $this->assertTrue($this->filter->canPass($links[0]));
    $this->assertFalse($this->filter->canPass($links[1]));
    $this->assertTrue($this->filter->canPass($links[2]));
    $this->assertFalse($this->filter->canPass($links[3]));
  }

  function testFilterAcceptedHosts()
  {
    $links = array(new lmbUri('http://www.test1.com/some/path'),
                   new lmbUri('http://test-broken.com'),
                   new lmbUri('http://test1.com'),
                   new lmbUri('http://microsoft.com'));

    $this->filter->allowProtocol('http');
    $this->filter->allowPathRegex('~.*~');
    $this->filter->allowHost('test1.com');
    $this->filter->allowHost('www.TEST1.com');//hosts are lowercased

    $this->assertTrue($this->filter->canPass($links[0]));
    $this->assertFalse($this->filter->canPass($links[1]));
    $this->assertTrue($this->filter->canPass($links[2]));
    $this->assertFalse($this->filter->canPass($links[3]));
  }

  function testFilterAcceptedPathsDefaultSettings()
  {
    $links = array(new lmbUri('http://test1.com/some/path'),
                   new lmbUri('http://test1.com/some/other/path'),
                   new lmbUri('http://test1.com/some/path/again'),
                   new lmbUri('http://test1.com/'));

    $this->filter->allowProtocol('http');
    $this->filter->allowHost('test1.com');

    $this->assertFalse($this->filter->canPass($links[0]));
    $this->assertFalse($this->filter->canPass($links[1]));
    $this->assertFalse($this->filter->canPass($links[2]));
    $this->assertFalse($this->filter->canPass($links[3]));
  }

  function testFilterAcceptedPaths()
  {
    $links = array(new lmbUri('http://test1.com/some/path'),
                   new lmbUri('http://test1.com/some/other/path'),
                   new lmbUri('http://test1.com/some/path/again'),
                   new lmbUri('http://test1.com/'));

    $this->filter->allowProtocol('http');
    $this->filter->allowHost('test1.com');
    $this->filter->allowPathRegex('~^/some/path.*$~');

    $this->assertTrue($this->filter->canPass($links[0]));
    $this->assertFalse($this->filter->canPass($links[1]));
    $this->assertTrue($this->filter->canPass($links[2]));
    $this->assertFalse($this->filter->canPass($links[3]));
  }

  function testFilterDisallowedPaths()
  {
    $links = array(new lmbUri('http://test1.com/some/path'),
                   new lmbUri('http://test1.com/some/other/path'),
                   new lmbUri('http://test1.com/some/path/again'),
                   new lmbUri('http://test1.com/'));

    $this->filter->allowProtocol('http');
    $this->filter->allowHost('test1.com');
    $this->filter->allowPathRegex('~^/some.*$~');
    $this->filter->disallowPathRegex('~^/some/path.*$~');

    $this->assertFalse($this->filter->canPass($links[0]));
    $this->assertTrue($this->filter->canPass($links[1]));
    $this->assertFalse($this->filter->canPass($links[2]));
    $this->assertFalse($this->filter->canPass($links[3]));
  }

}


