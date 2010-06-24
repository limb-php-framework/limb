<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbNotInArrayRule.class.php');

class lmbNotInArrayRuleTest extends lmbValidationRuleTestCase
{
  function testNotInArrayOk()
  {
    $rule = new lmbNotInArrayRule('testfield', array('www', 'ftp', 'smtp', 'mail'));

    $data = new lmbSet();
    $data->set('testfield', 'peaches');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testInArrayError()
  {
    $rule = new lmbNotInArrayRule('testfield', array('www', 'ftp', 'smtp', 'mail'));

    $data = new lmbSet();
    $data->set('testfield', 'www');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} has not allowed value.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));
 
    $rule->validate($data, $this->error_list);
  }

  function testInArrayCustomError()
  {
    $rule = new lmbNotInArrayRule('testfield', array('www', 'ftp', 'smtp', 'mail'), $error = 'my_custom_error');

    $data = new lmbSet();
    $data->set('testfield', 'www');

    $this->error_list->expectOnce('addError',
                                  array($error,
                                        array('Field'=>'testfield'),
                                        array()));
 
    $rule->validate($data, $this->error_list);
  }
}

