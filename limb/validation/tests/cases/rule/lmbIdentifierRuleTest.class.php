<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIdentifierRuleTest.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbIdentifierRule.class.php');

class lmbIdentifierRuleTest extends lmbValidationRuleTestCase
{
  function testValid()
  {
    $rule = new lmbIdentifierRule('test');

    $data = new lmbDataspace();
    $data->set('test', 'test');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);

  }

  function testValid2()
  {
    $rule = new lmbIdentifierRule('test');

    $data = new lmbDataspace();
    $data->set('test', 'test456');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);

  }

  function testNotValidContainsSpace()
  {
    $rule = new lmbIdentifierRule('test');

    $data = new lmbDataspace();
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

    $data = new lmbDataspace();
    $data->set('test', 'test/test');

    $this->error_list->expectOnce('addError',
                                 array(lmb_i18n('{Field} must contain only letters and numbers', 'validation'),
                                       array('Field' => 'test'),
                                       array()));

    $rule->validate($data, $this->error_list);
  }
}
?>