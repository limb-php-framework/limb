<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbNumericPrecisionRuleTest.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbNumericPrecisionRule.class.php');

class lmbNumericPrecisionRuleTest extends lmbValidationRuleTestCase
{
  function testNumericRule()
  {
    $rule = new lmbNumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '111.22');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleZero()
  {
    $rule = new lmbNumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '0');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleZeroInt()
  {
    $rule = new lmbNumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 0);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleFailure()
  {
    $rule = new lmbNumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'not a number');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be a valid number.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleTooManyWholeDigits()
  {
    $rule = new lmbNumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '1111');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('You have entered too many whole digits ({digits}) in {Field} (max {maxdigits}).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('maxdigits'=> 3, 'digits'=> 4)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleTooManyDecimalDigits()
  {
    $rule = new lmbNumericPrecisionRule('testfield', 3, 2);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '111.222');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('You have entered too many decimal digits ({digits}) in {Field} (max {maxdigits}).', 'validation'),
                                        array('Field' => 'testfield'),
                                        array('maxdigits' => 2, 'digits' => 3)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testNumericRuleFailureWithCustomError()
  {
    $rule = new lmbNumericPrecisionRule('testfield', 3, 2, 'Custom_Error');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'not a number');

    $this->error_list->expectOnce('addError',
                                  array('Custom_Error',
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }
}
?>