<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbRequiredRule.class.php');

class lmbRequiredRuleTest extends lmbValidationRuleTestCase
{
  function testRequiredRule()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', TRUE);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
    $this->assertTrue($rule->isValid());
  }

  function testRequiredRuleZero()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 0);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
    $this->assertTrue($rule->isValid());
  }

  function testRequiredRuleZeroString()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '0');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleFalse()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', FALSE);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleZeroLengthString()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '');

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} is required', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array()));

    $rule->validate($dataspace, $this->error_list);
    $this->assertFalse($rule->isValid());
  }

  function testRequiredRuleWithNull()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', NULL);

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} is required', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleWithSpacedString()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', "\n\t   \n\t");

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} is required', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleFailure()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbSet();

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} is required', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleFailureWithCustomError()
  {
    $rule = new lmbRequiredRule('testfield', 'Custom_Error');

    $dataspace = new lmbSet();

    $this->error_list->expectOnce('addError', array('Custom_Error',
                                                    array('Field'=>'testfield'),
                                                         array()));

    $rule->validate($dataspace, $this->error_list);
  }
}


