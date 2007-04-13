<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleDateRuleTest.class.php 5628 2007-04-11 12:09:20Z pachanga $
 * @package    i18n
 */
lmb_require('limb/validation/src/rule/lmbLocaleDateRule.class.php');
lmb_require('limb/validation/tests/cases/rule/lmbValidationRuleTestCase.class.php');

class lmbLocaleDateRuleTest extends lmbValidationRuleTestCase
{
  function testLocaleDateRuleCorrect()
  {
    $rule = new lmbLocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

    $data = new lmbSet(array('test' => '02/28/2003'));

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testLocaleDateRuleErrorLeapYear()
  {
    $rule = new lmbLocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

    $data = new lmbSet(array('test' => '02/29/2003'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must have a valid date format', 'validation'),
                                        array('Field' => 'test'), array()));

    $rule->validate($data, $this->error_list);
  }

  function testErrorLocaleMonthPosition()
  {
    $rule = new lmbLocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

    $data = new lmbSet(array('test' => '28/12/2003'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must have a valid date format', 'validation'),
                                        array('Field' => 'test'), array()));

    $rule->validate($data, $this->error_list);
  }

  function testLocaleDateRuleErrorFormat()
  {
    $rule = new lmbLocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

    $data = new lmbSet(array('test' => '02-29-2003'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must have a valid date format', 'validation'),
                                        array('Field' => 'test'), array()));

    $rule->validate($data, $this->error_list);
  }

  function testLocaleDateRuleError()
  {
    $rule = new lmbLocaleDateRule('test', new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/en.ini')));

    $data = new lmbSet(array('test' => '02jjklklak/sdsdskj34-sdsdsjkjkj78'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must have a valid date format', 'validation'),
                                        array('Field' => 'test'), array()));

    $rule->validate($data, $this->error_list);
  }
}

?>