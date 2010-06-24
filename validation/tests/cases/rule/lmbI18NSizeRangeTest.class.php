<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbI18NSizeRangeRule.class.php');

class lmbI18NSizeRangeTest extends lmbValidationRuleTestCase
{
  function testSizeRangeRuleEmpty()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 10);

    $data = new lmbSet();

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleBlank()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 5, 10);

    $data = new lmbSet(array('testfield' => ''));

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleZero()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 5, 10);

    $data = new lmbSet(array('testfield' => '0'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be greater than {min} and less than {max} characters.', 'validation'),
                                        array('Field' => 'testfield'),
                                        array('min' => 5, 'max' => 10)));

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleTooBig()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 3);

    $data = new lmbSet(array('testfield' => 'тест'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be less than {max} and greater than {min} characters.', 'validation'),
                                        array('Field' => 'testfield'),
                                        array('min' => null, 'max' => 3)));

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleTooBig2()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 2, 4);

    $data = new lmbSet(array('testfield' => 'тесты'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be less than {max} and greater than {min} characters.', 'validation'),
                                        array('Field' => 'testfield'),
                                        array('min' => 2, 'max' => 4)));

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleTooSmall()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 30, 100);

    $data = new lmbSet(array('testfield' => 'тест'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be greater than {min} and less than {max} characters.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('min' => 30, 'max' => 100)));

    $rule->validate($data, $this->error_list);
  }
}


