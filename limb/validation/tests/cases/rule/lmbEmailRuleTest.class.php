<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbEmailRule.class.php');

class lmbEmailRuleTest extends lmbValidationRuleTestCase
{
  function testEmailRule()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'billgates@microsoft.com');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleNoAt()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'billgatesmicrosoft.com');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must contain a @ character.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleInvalidUser()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'bill(y!)gates@microsoft.com');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('Invalid user in {Field}.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleInvalidDomain()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'billgates@micro$oft.com');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must contain only letters, numbers, hyphens, and periods.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleMixedCase()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'BillGates@Microsoft.com');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleSpecialChars()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'bill_gates.the-boss@microsoft.com');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleUnderscoreBeforeAt()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'bill_gates_@microsoft.com');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleDoubleErrorWithCustomMessage()
  {
    $rule = new lmbEmailRule('testfield',$error="my custom error");

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'не@правильное.мыло');

    $this->error_list->expectOnce('addError',
                                  array($error,
                                  array('Field'=>'testfield'),
                                  array()));

    $rule->validate($dataspace, $this->error_list);
  }
  
  function testEmailDoubleUnderscore() {
  	$rule = new lmbEmailRule('testfield');
  	$dataspace = new lmbSet();
  	$dataspace->set('testfield', '__ps__@mail.ru');
  	
  	$this->error_list->expectNever('addError');
  	$rule->validate($dataspace, $this->error_list);  	
  }
}


