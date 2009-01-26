<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 
lmb_require('limb/core/src/lmbArrayIterator.class.php');

class lmbMacroPaginateTagTest extends lmbBaseMacroTest
{
  protected $old_get;
  protected $old_server;

  function setUp()
  {
    parent :: setUp();

    $this->old_get = $_GET;
    $this->old_server = $_SERVER;

    $_SERVER['REQUEST_URI'] = 'test.com';
    $_GET = array();
  }

  function tearDown()
  {
    $_GET = $this->old_get;
    $_SERVER = $this->old_server;

    parent :: tearDown();
  }

  function testPaginateWithoutPager()
  {
    $template = '{{paginate iterator="$#test_iterator" offset="2" limit="2"/}}'.
                 '{{list using="$#test_iterator" as="$item"}}{{list:item}}{$item}{{/list:item}}{{/list}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('test_iterator', new lmbArrayIterator(array('Ivan', 'Pavel', 'Mike', 'Bob', 'Todd')));

    $expected = 'MikeBob';
    $this->assertEqual($page->render(), $expected);
  }
  
  function testErrorIfOffsetGivenWithoutLimit()
  {
    $template = '{{paginate iterator="$#test_iterator" offset="2"/}}'.
                 '{{list using="$#test_iterator" as="$item"}}{{list:item}}{$item}{{/list:item}}{{/list}}';

    try
    {
      $page = $this->_createMacroTemplate($template, 'tpl.html');
      $page->render();
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertTrue(true);
    }
  }  

  function testPaginateWithoutPagerAndOffsetIsZeroIfNotSpecified()
  {
    $template = '{{paginate iterator="$#test_iterator" limit="2"/}}'.
                 '{{list using="$#test_iterator" as="$item"}}{{list:item}}{$item}{{/list:item}}{{/list}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('test_iterator', new lmbArrayIterator(array('Ivan', 'Pavel', 'Mike', 'Bob', 'Todd')));

    $expected = 'IvanPavel';
    $this->assertEqual($page->render(), $expected);
  }
  
  function testPaginateWithPager()
  {
    $template = '{{paginate iterator="$#test_iterator" pager="test_pager"/}}'.
                 '{{list using="$#test_iterator" as="$item"}}{{list:item}}{$item}{{/list:item}}{{/list}}'.
                 '{{pager id="test_pager" items="2"/}}';

    $_GET['test_pager'] = 2; // offset = 2 since the second page
                  
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('test_iterator', new lmbArrayIterator(array('Ivan', 'Pavel', 'Mike', 'Bob', 'Todd')));

    $expected = 'MikeBob';
    $this->assertEqual($page->render(), $expected);
  }
  
  function testPaginateWithPagerOverwritesPagerItemsPerPageByLimitAttribute()
  {
    $template = '{{paginate iterator="$#test_iterator" pager="test_pager" limit="3"/}}'.
                 '{{list using="$#test_iterator" as="$item"}}{{list:item}}{$item}{{/list:item}}{{/list}}'.
                 '{{pager id="test_pager" items="2"/}}';

    $_GET['test_pager'] = 2; // offset = 2 since the second page
                  
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('test_iterator', new lmbArrayIterator(array('Ivan', 'Pavel', 'Mike', 'Bob', 'Todd', 'Serega')));

    $expected = 'BobToddSerega';
    $this->assertEqual($page->render(), $expected);
  }  

  function testPaginateWithPager_TotalItemsByAttribute()
  {
    $template = '{{paginate iterator="$#test_iterator" pager="test_pager" total_items="2"/}}'.
                 '{{list using="$#test_iterator" as="$item"}}{{list:item}}{$item}{{/list:item}}{{/list}}'.
                 '{{pager id="test_pager" items="2"/}}';

    $_GET['test_pager'] = 2; // offset = 2 since the second page
                  
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('test_iterator', new lmbArrayIterator(array('Ivan', 'Pavel', 'Mike', 'Bob', 'Todd')));

    $expected = 'IvanPavel'; // since forced to know about only 2 elements in array 
    $this->assertEqual($page->render(), $expected);
  }
}

