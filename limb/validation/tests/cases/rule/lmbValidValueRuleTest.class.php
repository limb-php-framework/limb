<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require('limb/validation/src/rule/lmbValidValueRule.class.php');

class lmbValidValueRuleTest extends lmbValidationRuleTestCase
{
  function testValidValueRule_Success_Int()
  {
    $rule = new lmbValidValueRule('testfield', 1);

    $data = new lmbSet();
    $data->set('testfield', 1);

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }
  
  function testValidValueRule_Error_Int()
  {
    $rule = new lmbValidValueRule('testfield', 1);

    $data = new lmbSet();
    $data->set('testfield', 0);

    $this->error_list->expectOnce('addError', array(
      lmb_i18n('{Field} value is wrong', 'validation'),
      array('Field' => 'testfield'),
      array()
    ));

    $rule->validate($data, $this->error_list);
  }

  function testValidValueRule_Success_IntAndString()
  {
    $rule = new lmbValidValueRule('testfield', 1);

    $data = new lmbSet();
    $data->set('testfield', '1');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }  

  function testInvalidValueRule_Success_Bool()
  {
    $rule = new lmbValidValueRule('testfield', false);

    $data = new lmbSet();
    $data->set('testfield', 0);

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);

  }  
}


