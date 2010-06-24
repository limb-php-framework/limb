<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/i18n/utf8.inc.php');

class lmbI18NCapitalizeMacroFilterTest extends lmbBaseMacroTest
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

  function testCapitalize()
  {
    $code = '{$#var|i18n_capitalize}';
    $tpl = $this->_createMacroTemplate($code, 'capitalize.html');
    $var = "что-то";
    $tpl->set('var', $var);
    $out = $tpl->render();
    $this->assertEqual($out, 'Что-то');
  }
}

