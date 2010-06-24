<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/i18n/utf8.inc.php');

class lmbI18NClipFilterTest extends lmbWactTestCase
{
  var $prev_driver;

  function setUp()
  {
    parent :: setUp();
    $this->prev_driver = lmb_use_charset_driver(new lmbUTF8BaseDriver());
  }

  function tearDown()
  {
    lmb_use_charset_driver($this->prev_driver);
    parent :: tearDown();
  }

  function testLengthLimit()
  {
    $template = '{$"что-то"|i18n_clip:3}';

    $this->registerTestingTemplate('/limb/clip_with_limit.html', $template);

    $page = $this->initTemplate('/limb/clip_with_limit.html');

    $this->assertEqual($page->capture(), 'что');
  }

  function testLengthLimitAndOffset()
  {
    $template = '{$"фреймворк для веб-приложений"|i18n_clip:3,5}';

    $this->registerTestingTemplate('/limb/clip_with_offset.html', $template);

    $page = $this->initTemplate('/limb/clip_with_offset.html');

    $this->assertEqual($page->capture(), 'вор');
  }

  function testWithSuffix()
  {
    $template = '{$"фреймворк для веб-приложений"|i18n_clip:3,5,"..."}';

    $this->registerTestingTemplate('/limb/clip_with_suffix.html', $template);

    $page = $this->initTemplate('/limb/clip_with_suffix.html');

    $this->assertEqual($page->capture(), 'вор...');
  }

  function testSuffixNotUsedTooShortString()
  {
    $template = '{$"фреймворк"|i18n_clip:10,"0","..."}';

    $this->registerTestingTemplate('/limb/clip_terminator_not_used.html', $template);

    $page = $this->initTemplate('/limb/clip_terminator_not_used.html');

    $this->assertEqual($page->capture(), "фреймворк");
  }

  // can't implement this since PHP has some bugs with /b modifier in multibyte mode
  function _testLongStringWordBoundary()
  {
    $template = '{$"фреймворк для веб-приложений"|i18n_clip:11,1,"...", "y"}';

    $this->registerTestingTemplate('/limb/clip_with_word_bound.html', $template);

    $page = $this->initTemplate('/limb/clip_with_word_bound.html');

    $this->assertEqual($page->capture(), 'реймворк для...');
  }

  function testDBELengthLimit()
  {
    $template = '{$var|i18n_clip:3}';

    $this->registerTestingTemplate('/limb/clip_dbe_with_limit.html', $template);

    $page = $this->initTemplate('/limb/clip_dbe_with_limit.html');
    $page->set('var', 'что-то');

    $this->assertEqual($page->capture(), 'что');
  }

  function testDBELengthLimitAndOffset()
  {
    $template = '{$var|i18n_clip:3,5}';

    $this->registerTestingTemplate('/limb/clip_dbe_with_offset.html', $template);

    $page = $this->initTemplate('/limb/clip_dbe_with_offset.html');
    $page->set('var', "фреймворк для веб-приложений");

    $this->assertEqual($page->capture(), 'вор');
  }

  function testDBEWithSuffix()
  {
    $template = '{$var|i18n_clip:3,5,"..."}';

    $this->registerTestingTemplate('/limb/clip_dbe_with_suffix.html', $template);

    $page = $this->initTemplate('/limb/clip_dbe_with_suffix.html');
    $page->set('var', "фреймворк для веб-приложений");

    $this->assertEqual($page->capture(), 'вор...');
  }

  function testDBESuffixNotUsedTooShortString()
  {
    $template = '{$var|i18n_clip:10,"0","..."}';

    $this->registerTestingTemplate('/limb/clip_dbe_suffix_not_used.html', $template);

    $page = $this->initTemplate('/limb/clip_dbe_suffix_not_used.html');
    $page->set('var', "фреймворк");

    $this->assertEqual($page->capture(), "фреймворк");
  }

  function testPathBasedDBELengthLimit()
  {
    $template = '{$my.var|i18n_clip:3}';

    $this->registerTestingTemplate('/limb/clip_path_based_dbe_with_limit.html', $template);

    $page = $this->initTemplate('/limb/clip_path_based_dbe_with_limit.html');

    $my_dataspace = new lmbSet(array('var' => 'что-то'));
    $page->set('my', $my_dataspace);

    $this->assertEqual($page->capture(), 'что');
  }

}

