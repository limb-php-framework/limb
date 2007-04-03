<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbContentTypeFilterTest.class.php 5014 2007-02-08 15:38:18Z pachanga $
 * @package    web_spider
 */
lmb_require('limb/web_spider/src/lmbContentTypeFilter.class.php');

class lmbContentTypeFilterTest extends UnitTestCase
{
  var $filter;

  function setUp()
  {
    $this->filter = new lmbContentTypeFilter();
  }

  function testFilterAcceptedContentTypes()
  {
    $this->filter->allowContentType('html/text');
    $this->filter->allowContentType('xml/text');

    $this->assertTrue($this->filter->canPass('html/text'));
    $this->assertFalse($this->filter->canPass('image/png'));
    $this->assertTrue($this->filter->canPass('html/text'));
  }
}

?>
