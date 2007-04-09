<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPatternRuleTest.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbPatternRule.class.php');

class lmbPatternRuleTest extends lmbValidationRuleTestCase
{
  function testPatternRule()
  {
    $rule = new lmbPatternRule('testfield', '/^\w+$/');

    $data = new lmbDataspace();
    $data->set('testfield', 'SimpletestisCool');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testPatternRuleFailed()
  {
    $rule = new lmbPatternRule('testfield', '/^\w+$/');

    $data = new lmbDataspace();
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

    $data = new lmbDataspace();
    $data->set('testfield', 'Simpletest is Cool!');

    $this->error_list->expectOnce('addError',
                                  array('Custom_Error',
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($data, $this->error_list);
  }
}
?>