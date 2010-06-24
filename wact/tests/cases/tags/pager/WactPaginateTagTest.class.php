<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactPaginateTagTest extends WactTemplateTestCase
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

  function testPaginateList()
  {
    $template =
                '<paginate list="test_list" with="test" />'.
                '<pager:NAVIGATOR id="test" items="3">'.
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$href}|{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$href}|{$number}|</pager:NUMBER>' .
                '<pager:SEPARATOR>**</pager:SEPARATOR>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>'.
                '<list:list id="test_list"><list:item>{$name}|</list:item></list:list>';

    $this->registerTestingTemplate('/tags/pager/paginate_list.html', $template);

    $page = $this->initTemplate('/tags/pager/paginate_list.html');

    $_GET['page_test'] = 2;

    $expected = 'N-test.com|1|**'.
                'C-test.com?page_test=2|2|'.
                'John|Robert|';

    $page->setChildDataset('test_list', array(array('name' => 'Pavel'),
                                              array('name' => 'Mike'),
                                              array('name' => 'Sergey'),
                                              array('name' => 'John'),
                                              array('name' => 'Robert')));

    $this->assertEqual($page->capture(), $expected);
  }
}

