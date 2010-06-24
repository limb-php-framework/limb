<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbBaseValidationRule.class.php');
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');
lmb_require('limb/validation/src/lmbValidator.class.php');

class lmbStubBaseValidationRule extends lmbBaseValidationRule
{
  public $result = true;
  public $validate_called = false;

  protected function _doValidate($datasource)
  {
    $this->validate_called = true;
    if(!$this->result)
      $this->error('Some error');
  }
}

class lmbConditionalValidationTest extends lmbValidationRuleTestCase
{
  function testValidate()
  {
    $rule = new lmbStubBaseValidationRule();

    $this->assertTrue($rule->validate(new lmbSet(), $this->error_list));
    $this->assertTrue($rule->isValid());
  }

  function testValidateNotValid()
  {
    $rule = new lmbStubBaseValidationRule();
    $rule->result = false;

    $this->assertFalse($rule->validate(new lmbSet(), $this->error_list));
    $this->assertFalse($rule->isValid());
  }

}


