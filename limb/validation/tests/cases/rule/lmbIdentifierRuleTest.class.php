<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbIdentifierRule.class.php');

class lmbIdentifierRuleTest extends lmbValidationRuleTestCase
{
  function testValid()
  {
    $rule = new lmbIdentifierRule('test');

    $data = new lmbSet();
    $data->set('test', 'test');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);

  }

  function testValid2()
  {
    $rule = new lmbIdentifierRule('test');

    $data = new lmbSet();
    $data->set('test', 'test456');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);

  }

  function testNotValidContainsSpace()
  {
    $rule = new lmbIdentifierRule('test');

    $data = new lmbSet();
    $data->set('test', 'test test');

    $this->error_list->expectOnce('addError',
                                 array(lmb_i18n('{Field} must contain only letters and numbers', 'validation'),
                                       array('Field' => 'test'),
                                       array()));

    $rule->validate($data, $this->error_list);
  }

  function testNotValidContainsSlash()
  {
    $rule = new lmbIdentifierRule('test');

    $data = new lmbSet();
    $data->set('test', 'test/test');

    $this->error_list->expectOnce('addError',
                                 array(lmb_i18n('{Field} must contain only letters and numbers', 'validation'),
                                       array('Field' => 'test'),
                                       array()));

    $rule->validate($data, $this->error_list);
  }
}

