<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbPatternRule.class.php');

class lmbPatternRuleTest extends lmbValidationRuleTestCase
{
  function testPatternRule()
  {
    $rule = new lmbPatternRule('testfield', '/^\w+$/');

    $data = new lmbSet();
    $data->set('testfield', 'SimpletestisCool');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testPatternRuleFailed()
  {
    $rule = new lmbPatternRule('testfield', '/^\w+$/');

    $data = new lmbSet();
    $data->set('testfield', 'Simpletest is Cool!');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} value is wrong', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($data, $this->error_list);
  }

  function testPatternRuleFailedWithCustomError()
  {
    $rule = new lmbPatternRule('testfield', '/^\w+$/', 'Custom_Error');

    $data = new lmbSet();
    $data->set('testfield', 'Simpletest is Cool!');

    $this->error_list->expectOnce('addError',
                                  array('Custom_Error',
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($data, $this->error_list);
  }
}

