<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSizeRangeRuleTest.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbSizeRangeRule.class.php');

class lmbSizeRangeRuleTest extends lmbValidationRuleTestCase
{
  function testSizeRangeRuleEmpty()
  {
    $rule = new lmbSizeRangeRule('testfield', 10);

    $dataspace = new lmbDataspace();

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleBlank()
  {
    $rule = new lmbSizeRangeRule('testfield', 5, 10);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleZero()
  {
    $rule = new lmbSizeRangeRule('testfield', 5, 10);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '0');

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} must be greater than {min} characters.', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array('min'=>5)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooBig()
  {
    $rule = new lmbSizeRangeRule('testfield', 10);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError', array(lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                                                         array('Field'=>'testfield'),
                                                         array('max'=>10)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooBig2()
  {
    $rule = new lmbSizeRangeRule('testfield', 5, 10);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('max'=>10)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testSizeRangeRuleTooSmall()
  {
    $rule = new lmbSizeRangeRule('testfield', 30, 100);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '12345678901234567890');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must be greater than {min} characters.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('min'=>30)));

    $rule->validate($dataspace, $this->error_list);
  }
}

?>