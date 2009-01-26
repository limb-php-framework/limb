<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/i18n/utf8.inc.php');
lmb_require('limb/core/src/lmbSet.class.php');

class lmbI18NClipMacroFilterTest extends lmbBaseMacroTest
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
    $code = '{$#var|i18n_clip:3}';
    $tpl = $this->_createMacroTemplate($code, 'length_limit.html');
    $var = "что-то";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEqual($out, 'что');
  }
  
  
  function testLengthLimitAsVariable()
  {
    $code = '{$#var|i18n_clip:$#limit}';
    $tpl = $this->_createMacroTemplate($code, 'length_limit.html');
    $var = "что-то";
    $tpl->set('var', $var);
    $tpl->set('limit', 3);
    $out = $tpl->render();
    $this->assertEqual($out, 'что');
  }

  function testLengthLimitAndOffset()
  {
    $code = '{$#var|i18n_clip:3,5}';
    $tpl = $this->_createMacroTemplate($code, 'length_limit_and_offset.html');
    $var = "фреймворк для веб-приложений";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEqual($out, 'вор');
  }

  function testWithSuffix()
  {
    $code = '{$#var|i18n_clip:3,5,"..."}';
    $tpl = $this->_createMacroTemplate($code, 'clip_with_suffix.html');
    $var = "фреймворк для веб-приложений";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEqual($out, 'вор...');
  }

  function testSuffixNotUsedTooShortString()
  {
    $code = '{$#var|i18n_clip:10,"0","..."}';
    $tpl = $this->_createMacroTemplate($code, 'clip_suffix_not_used.html');
    $var = "фреймворк";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEqual($out, 'фреймворк');
  }

  // don't know if boundary condition works for all cases. Should work for the simple ones.
  function testLongStringWordBoundary()
  {
    $code = '{$#var|i18n_clip:12,0,"...", "y"}';
    $tpl = $this->_createMacroTemplate($code, 'clip_with_word_bound.html');
    $var = "фреймворк для веб-приложений";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEqual($out, 'фреймворк для...');
  }

  function testPathBasedDBELengthLimit()
  {
    $code = '{$#my.var|i18n_clip:3}';
    $tpl = $this->_createMacroTemplate($code, 'clip_path_based_dbe_with_limit.html');
    $data = new lmbSet(array('var' => 'что-то'));
    $tpl->set('my', $data);
    $out = $tpl->render();
    $this->assertEqual($out, 'что');
  }

  function testQuoteRegexPatterns()
  {
    $code = '{$#var|i18n_clip:16,0,"...", "y"}';
    $tpl = $this->_createMacroTemplate($code, 'clip_with_regex_pattern.html');
    $var = "(фреймворк.*) для веб-приложений";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEqual($out, '(фреймворк.*) для...');
  }
}

