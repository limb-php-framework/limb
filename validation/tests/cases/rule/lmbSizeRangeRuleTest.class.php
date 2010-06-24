<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbSizeRangeRule.class.php');

class lmbSizeRangeRuleTest extends lmbValidationRuleTestCase
{
  function testSizeRangeRuleEmpty()
  {
    $rule = new lmbSizeRangeRule('testfield', 10);

    $dataspace = new lmbSet();

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleBlank()
  {
    $rule = new lmbSizeRangeRule('testfield', 5, 10);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleZero()
  {
    $rule = new lmbSizeRangeRule('testfield', 5, 10);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '0');

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} must be greater than {min} characters.', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array('min'=>5,
                                                               'max' =>10)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooBig()
  {
    $rule = new lmbSizeRangeRule('testfield', 10);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array('max'=>10,
                                                               'min' => NULL)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooBig2()
  {
    $rule = new lmbSizeRangeRule('testfield', 5, 10);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('max'=>10,
                                              'min' => 5)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooSmall()
  {
    $rule = new lmbSizeRangeRule('testfield', 30, 100);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be greater than {min} characters.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('min'=>30,
                                              'max' => 100)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooBigWithCustomErrorMessage()
  {
    $rule = new lmbSizeRangeRule('testfield', 5, 10, 'Error_custom');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError',
                                  array('Error_custom',
                                        array('Field'=>'testfield'),
                                        array('max'=>10,
                                              'min' => 5)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooSmallWithCustomErrorMessage()
  {
    $rule = new lmbSizeRangeRule('testfield', 30, 100, 'Error_custom');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError',
                                  array('Error_custom',
                                        array('Field'=>'testfield'),
                                        array('min'=>30,
                                              'max' => 100)));

    $rule->validate($dataspace, $this->error_list);
  }
}


