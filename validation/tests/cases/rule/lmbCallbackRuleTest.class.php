<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbCallbackRule.class.php');

class lmbCallbackRuleTest extends lmbValidationRuleTestCase
{
  protected $validate_callback_result = true;
  protected $validate_called = false;

  function testValidate()
  {
    $rule = new lmbCallbackRule($this, 'validate');

    $this->assertTrue($rule->validate(new lmbSet(), $this->error_list));
    $this->assertTrue($rule->isValid());
  }

  function testValidateNotValid()
  {
    $rule = new lmbCallbackRule($this, 'validate');
    $this->validate_callback_result = false;

    $this->assertFalse($rule->validate(new lmbSet(), $this->error_list));
    $this->assertFalse($rule->isValid());
  }

  function validate($datasource, $error_list)
  {
    $this->validate_called = true;
    if(!$this->validate_callback_result)
      $error_list->addError('Some error');
    return $this->validate_callback_result;
  }
}


