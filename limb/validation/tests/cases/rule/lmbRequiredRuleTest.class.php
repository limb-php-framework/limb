<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequiredRuleTest.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbRequiredRule.class.php');

class lmbRequiredRuleTest extends lmbValidationRuleTestCase
{
  function testRequiredRule()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', TRUE);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleZero()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 0);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleZeroString()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '0');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleFalse()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', FALSE);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleZeroLengthString()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '');

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} is required', 'validation'),
                                                         array('Field'=>'testfield')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleWithNull()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', NULL);

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} is required', 'validation'),
                                                         array('Field'=>'testfield')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleFailure()
  {
    $rule = new lmbRequiredRule('testfield');

    $dataspace = new lmbDataspace();

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} is required', 'validation'),
                                                         array('Field'=>'testfield')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testRequiredRuleFailureWithCustomError()
  {
    $rule = new lmbRequiredRule('testfield', 'Custom_Error');

    $dataspace = new lmbDataspace();

    $this->error_list->expectOnce('addError', array('Custom_Error',
                                                    array('Field'=>'testfield')));

    $rule->validate($dataspace, $this->error_list);
  }
}

?>