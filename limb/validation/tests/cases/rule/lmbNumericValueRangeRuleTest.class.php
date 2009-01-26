<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbNumericValueRangeRule.class.php');

class lmbNumericValueRangeRuleTest extends lmbValidationRuleTestCase
{
  function testInRange()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 0, 5);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 1);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testLessThanMin()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 1, 5);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', -10);

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be not less than {value}.', 'validation'),
                                        array('Field' => 'testfield'),
                                        array('value' => 1)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testGreaterThanMax()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 1, 5);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 10);

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be not greater than {value}.', 'validation'),
                                        array('Field' => 'testfield'),
                                        array('value' => 5)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testOnlyDigitsAllowedNumeric()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 1, 4);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '4fdfasd');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be a valid number.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testLessThanMinWithCustomError()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 1, 5, 'Custom_Error');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', -10);

    $this->error_list->expectOnce('addError',
                                  array('Custom_Error',
                                        array('Field' => 'testfield'),
                                        array('value' => 1)));

    $rule->validate($dataspace, $this->error_list);
  }
}

