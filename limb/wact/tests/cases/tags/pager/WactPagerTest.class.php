<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactPagerTest extends WactTemplateTestCase
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

  function testSetParameters()
  {
    $template = '<pager:NAVIGATOR id="test" items="25" pages_per_section="4" pager_prefix="nav">'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/navigator_default.html', $template);

    $page = $this->initTemplate('/tags/pager/navigator_default.html');

    $pager = $page->getChild('test');

    $this->assertEqual($pager->getItemsPerPage(), 25);
    $this->assertEqual($pager->getPagesPerSection(), 4);
    $this->assertEqual($pager->getPagerId(), 'nav_test');
  }

  function testPager()
  {
    $template = '<pager:NAVIGATOR id="test" items="10">'.
                '<pager:FIRST>F-{$href}|</pager:FIRST>' .
                '<pager:PREV>P-{$href}|</pager:PREV>' .
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$href}|{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$href}|{$number}|</pager:NUMBER>' .
                '<pager:SEPARATOR>**</pager:SEPARATOR>' .
                '</pager:LIST>'.
                '<pager:NEXT>X-{$href}|</pager:NEXT>' .
                '<pager:LAST>L-{$href}|</pager:LAST>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/simple.html', $template);

    $page = $this->initTemplate('/tags/pager/simple.html');

    $_GET['page_test'] = 2;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(40);
    $navigator->prepare();

    $expected = 'F-test.com|' .
                'P-test.com|'.
                'N-test.com|1|**'.
                'C-test.com?page_test=2|2|**'.
                'N-test.com?page_test=3|3|**N-test.com?page_test=4|4|'.
                'X-test.com?page_test=3|'.
                'L-test.com?page_test=4|';

    $this->assertEqual($page->capture(), $expected);
  }

  function testPagerProperties()
  {
    $template = '<pager:NAVIGATOR id="test" items="5">'.
                '{$TotalItems}|{$TotalPages}|' .
                '<core:OPTIONAL for="HasMoreThanOnePage">yes|</core:OPTIONAL>' .
                'from:{$BeginItemNumber}|to:{$EndItemNumber}' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/props.html', $template);

    $page = $this->initTemplate('/tags/pager/props.html');

    $_GET['page_test'] = 2;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(40);
    $navigator->prepare();

    $expected = '40|8|yes|from:6|to:10';

    $this->assertEqual($page->capture(), $expected);
  }

  function testSinglePage()
  {
    $template = '<pager:NAVIGATOR id="test" items="10">'.
                '<pager:FIRST>F-{$href}|</pager:FIRST>' .
                '<pager:PREV>P-{$href}|</pager:PREV>' .
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$href}|{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$href}|{$number}|</pager:NUMBER>' .
                '</pager:LIST>'.
                '<pager:NEXT>X-{$href}|</pager:NEXT>' .
                '<pager:LAST>L-{$href}|</pager:LAST>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/one_page_only.html', $template);

    $page = $this->initTemplate('/tags/pager/one_page_only.html');

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(5);
    $navigator->prepare();

    $expected = '';

    $this->assertEqual($page->capture(), $expected);
  }

  function testFistPage()
  {
    $template = '<pager:NAVIGATOR id="test" items="10">'.
                '<pager:FIRST>F-{$href}|</pager:FIRST>' .
                '<pager:PREV>P-{$href}|</pager:PREV>' .
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$href}|{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$href}|{$number}|</pager:NUMBER>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/first_page.html', $template);

    $page = $this->initTemplate('/tags/pager/first_page.html');

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(40);
    $navigator->prepare();

    $expected = 'C-test.com|1|'.
                'N-test.com?page_test=2|2|N-test.com?page_test=3|3|N-test.com?page_test=4|4|';

    $this->assertEqual($page->capture(), $expected);
  }

  function testLastPage()
  {
    $template = '<pager:NAVIGATOR id="test" items="10">'.
                '<pager:FIRST>F-{$href}|</pager:FIRST>' .
                '<pager:PREV>P-{$href}|</pager:PREV>' .
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$href}|{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$href}|{$number}|</pager:NUMBER>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/last_page.html', $template);

    $page = $this->initTemplate('/tags/pager/last_page.html');

    $_GET['page_test'] = 3;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(30);
    $navigator->prepare();

    $expected = 'F-test.com|' .
                'P-test.com?page_test=2|'.
                'N-test.com|1|'.
                'N-test.com?page_test=2|2|'.
                'C-test.com?page_test=3|3|';

    $this->assertEqual($page->capture(), $expected);
  }

  function testElipsesBothSides()
  {
    $template = '<pager:NAVIGATOR id="test" items="5" pages_in_middle="3" pages_in_sides="3">'.
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$number}|</pager:NUMBER>' .
                '<pager:ELIPSES>...</pager:ELIPSES>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/elipses_both_sides.html', $template);

    $page = $this->initTemplate('/tags/pager/elipses_both_sides.html');

    $_GET['page_test'] = 7;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(65);
    $navigator->prepare();

    $expected = 'N-1|N-2|N-3|...N-6|C-7|N-8|...N-11|N-12|N-13|';
    $this->assertEqual($page->capture(), $expected);
  }

  function testElipsesBothSidesAndSeparator()
  {
    $template = '<pager:NAVIGATOR id="test" items="5" pages_in_middle="3" pages_in_sides="3">'.
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$number}</pager:CURRENT>' .
                '<pager:NUMBER>N-{$number}</pager:NUMBER>' .
                '<pager:ELIPSES>...</pager:ELIPSES>' .
                '<pager:SEPARATOR>|</pager:SEPARATOR>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/elipses_both_sides_with_separator.html', $template);

    $page = $this->initTemplate('/tags/pager/elipses_both_sides_with_separator.html');

    $_GET['page_test'] = 7;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(65);
    $navigator->prepare();

    $expected = 'N-1|N-2|N-3...N-6|C-7|N-8...N-11|N-12|N-13';
    $this->assertEqual($page->capture(), $expected);
  }

  function testElipsesBothSidesNoPagesInSides()
  {
    $template = '<pager:NAVIGATOR id="test" items="5" pages_in_middle="3" pages_in_sides="0">'.
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$number}|</pager:NUMBER>' .
                '<pager:ELIPSES>...</pager:ELIPSES>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/elipses_both_sides_no_paged_in_sides.html', $template);

    $page = $this->initTemplate('/tags/pager/elipses_both_sides_no_paged_in_sides.html');

    $_GET['page_test'] = 7;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(65);
    $navigator->prepare();

    $expected = '...N-6|C-7|N-8|...';
    $this->assertEqual($page->capture(), $expected);
  }

  function testElipsesLeftOnly()
  {
    $template = '<pager:NAVIGATOR id="test" items="5" pages_in_middle="3" pages_in_sides="3">'.
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$number}|</pager:NUMBER>' .
                '<pager:ELIPSES>...</pager:ELIPSES>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/elipses_left_only.html', $template);

    $page = $this->initTemplate('/tags/pager/elipses_left_only.html');

    $_GET['page_test'] = 7;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(55);
    $navigator->prepare();

    $expected = 'N-1|N-2|N-3|...N-6|C-7|N-8|N-9|N-10|N-11|';
    $this->assertEqual($page->capture(), $expected);
  }

  function testNoElipsesForSingleItemGap()
  {
    $template = '<pager:NAVIGATOR id="test" items="5" pages_in_middle="3" pages_in_sides="3">'.
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$number}|</pager:NUMBER>' .
                '<pager:ELIPSES>...</pager:ELIPSES>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/elipses_left_only.html', $template);

    $page = $this->initTemplate('/tags/pager/elipses_left_only.html');

    $_GET['page_test'] = 6;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(35);
    $navigator->prepare();

    $expected = 'N-1|N-2|N-3|N-4|N-5|C-6|N-7|';
    $this->assertEqual($page->capture(), $expected);
  }

  function testSections()
  {
    $template = '<pager:NAVIGATOR id="test" items="10" pages_per_section="2">'.
                '<pager:LIST>'.
                '<pager:CURRENT>C-{$href}|{$number}|</pager:CURRENT>' .
                '<pager:NUMBER>N-{$href}|{$number}|</pager:NUMBER>' .
                '<pager:SECTION>S-{$href}|{$number_begin}|{$number_end}|</pager:SECTION>' .
                '</pager:LIST>'.
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/sections.html', $template);

    $page = $this->initTemplate('/tags/pager/sections.html');

    $_GET['page_test'] = 3;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(60);
    $navigator->prepare();

    $expected = 'S-test.com?page_test=2|1|2|' .
                'C-test.com?page_test=3|3|'.
                'N-test.com?page_test=4|4|'.
                'S-test.com?page_test=5|5|6|';

    $this->assertEqual($page->capture(), $expected);
  }

  function testMirror()
  {
    $template = '<pager:NAVIGATOR id="test1" items="10">'.
                '<pager:PREV>P1-{$href}|</pager:PREV>' .
                '</pager:NAVIGATOR>'. //note, mirror settings override source!!!
                '<pager:NAVIGATOR id="test2" mirror="test1" items="30">'.
                '<pager:PREV>P2-{$href}|</pager:PREV>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/navigator_mirror.html', $template);

    $page = $this->initTemplate('/tags/pager/navigator_mirror.html');

    $_GET['page_test1'] = 3;

    $navigator = $page->findChild('test1');
    $navigator->setTotalItems(60);
    $navigator->prepare();

    $expected = "P1-test.com?page_test1=2|P2-test.com?page_test1=2|";
    $this->assertEqual($page->capture(), $expected);
  }

  function testMirrorMultiplePages()
  {
    $template = '<pager:NAVIGATOR id="test1" items="10">'.
                '<pager:PREV>P1-{$href}|</pager:PREV>' .
                '<pager:LIST>' .
                '<pager:CURRENT>*{$number}*</pager:CURRENT>' .
                '<pager:NUMBER>/{$number}/</pager:NUMBER>' .
                '</pager:LIST>' .
                '<pager:NEXT>N1-{$href}|</pager:NEXT>' .
                '</pager:NAVIGATOR>'. //note, mirror settings override source!!!

                '<pager:NAVIGATOR id="test2" mirror="test1" items="10">'.
                '<pager:PREV>P2-{$href}:</pager:PREV>' .
                '<pager:LIST>' .
                '<pager:CURRENT>**{$number}**</pager:CURRENT>' .
                '<pager:NUMBER>//{$number}//</pager:NUMBER>' .
                '</pager:LIST>' .
                '<pager:NEXT>N2-{$href}:</pager:NEXT>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/navigator_mirror_multiple_pages.html', $template);

    $page = $this->initTemplate('/tags/pager/navigator_mirror_multiple_pages.html');

    $_GET['page_test1'] = 3;

    $navigator = $page->findChild('test1');
    $navigator->setTotalItems(60);
    $navigator->prepare();

    $expected = "P1-test.com?page_test1=2|/1//2/*3*/4//5//6/N1-test.com?page_test1=4|" .
                "P2-test.com?page_test1=2://1////2//**3**//4////5////6//N2-test.com?page_test1=4:";
    $this->assertEqual($page->capture(), $expected);
  }

  function testDisabledTagsShown()
  {
    $template = '<pager:NAVIGATOR id="test" items="10">'.
                '<pager:FIRST:DISABLED>F-|</pager:FIRST:DISABLED>' .
                '<pager:PREV:DISABLED>P-|</pager:PREV:DISABLED>' .
                '<pager:NEXT:DISABLED>|-X</pager:NEXT:DISABLED>' .
                '<pager:LAST:DISABLED>|-L</pager:LAST:DISABLED>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/test_disabled_tags_shown.html', $template);

    $page = $this->initTemplate('/tags/pager/test_disabled_tags_shown.html');

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(1);
    $navigator->prepare();

    $expected = 'F-|P-||-X|-L';

    $this->assertEqual($page->capture(), $expected);
  }

  function testDisabledTagsHidden()
  {
    $template = '<pager:NAVIGATOR id="test" items="1">'.
                '<pager:FIRST:DISABLED>F-|</pager:FIRST:DISABLED>' .
                '<pager:PREV:DISABLED>P-|</pager:PREV:DISABLED>' .
                '<pager:NEXT:DISABLED>|-X</pager:NEXT:DISABLED>' .
                '<pager:LAST:DISABLED>|-L</pager:LAST:DISABLED>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/test_disabled_tags_hidden.html', $template);

    $page = $this->initTemplate('/tags/pager/test_disabled_tags_hidden.html');

    $_GET['page_test'] = 2;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(3);
    $navigator->prepare();

    $expected = '';

    $this->assertEqual($page->capture(), $expected);
  }

  function testShowFirstAndPrevTagsDisabledOnly()
  {
    $template = '<pager:NAVIGATOR id="test" items="10">'.
                '<pager:FIRST:DISABLED>F-|</pager:FIRST:DISABLED>' .
                '<pager:PREV:DISABLED>P-|</pager:PREV:DISABLED>' .
                '<pager:NEXT:DISABLED>|-X</pager:NEXT:DISABLED>' .
                '<pager:LAST:DISABLED>|-L</pager:LAST:DISABLED>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/test_first_and_prev_disabled_only.html', $template);

    $page = $this->initTemplate('/tags/pager/test_first_and_prev_disabled_only.html');

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(20);
    $navigator->prepare();

    $expected = 'F-|P-|';

    $this->assertEqual($page->capture(), $expected);
  }

  function testShowNextAndLastTagsDisabledOnly()
  {
    $template = '<pager:NAVIGATOR id="test" items="10">'.
                '<pager:FIRST:DISABLED>F-|</pager:FIRST:DISABLED>' .
                '<pager:PREV:DISABLED>P-|</pager:PREV:DISABLED>' .
                '<pager:NEXT:DISABLED>|-X</pager:NEXT:DISABLED>' .
                '<pager:LAST:DISABLED>|-L</pager:LAST:DISABLED>' .
                '</pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/pager/test_next_and_last_disabled_only.html', $template);

    $page = $this->initTemplate('/tags/pager/test_next_and_last_disabled_only.html');

    $_GET['page_test'] = 2;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(20);
    $navigator->prepare();

    $expected = '|-X|-L';

    $this->assertEqual($page->capture(), $expected);
  }

  /**
   * Ensure pager mirror can find its original pager even if they have different parent nodes.
   */
  function testPagerMirror()
  {
    $pager_contents = '<pager:LIST>'.
                      '<pager:CURRENT>C-{$href}|{$number}|</pager:CURRENT>' .
                      '<pager:NUMBER>N-{$href}|{$number}|</pager:NUMBER>' .
                      '<pager:SEPARATOR>**</pager:SEPARATOR>' .
                      '</pager:LIST>';

    $template = '<core:BLOCK>'.
                '<pager:NAVIGATOR id="test" items="10">'. $pager_contents .'</pager:NAVIGATOR>'.
                '</core:BLOCK>'.
                '<core:BLOCK>'.
                '<pager:NAVIGATOR mirror="test">'. $pager_contents . '</pager:NAVIGATOR>'.
                '</core:BLOCK>';

    $this->registerTestingTemplate('/tags/pager/mirror.html', $template);

    $page = $this->initTemplate('/tags/pager/mirror.html');

    $_GET['page_test'] = 2;

    $navigator = $page->findChild('test');
    $navigator->setTotalItems(40);
    $navigator->prepare();

    $result = 'N-test.com|1|**'.
              'C-test.com?page_test=2|2|**'.
              'N-test.com?page_test=3|3|**N-test.com?page_test=4|4|';
    $this->assertEqual($page->capture(), $result . $result);
  }
}

