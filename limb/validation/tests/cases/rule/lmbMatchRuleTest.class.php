<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMatchRuleTest.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbMatchRule.class.php');

class lmbMatchRuleTest extends lmbValidationRuleTestCase
{
  function testMatchRule()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'peaches');
    $dataspace->set('testmatch', 'peaches');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleEmpty()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleEmpty2()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'peaches');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleEmpty3()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testmatch', 'peaches');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleFailure()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'peaches');
    $dataspace->set('testmatch', 'cream');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} does not match {MatchField}.', 'validation'),
                                        array('Field' => 'testfield', 'MatchField' => 'testmatch')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleFailureWithCustomError()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch', 'Custom_Error');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'peaches');
    $dataspace->set('testmatch', 'cream');

    $this->error_list->expectOnce('addError',
                                  array('Custom_Error',
                                        array('Field' => 'testfield', 'MatchField' => 'testmatch')));

    $rule->validate($dataspace, $this->error_list);
  }
}
?>