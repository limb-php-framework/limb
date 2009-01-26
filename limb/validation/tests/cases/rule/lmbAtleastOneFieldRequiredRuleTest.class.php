<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbAtleastOneFieldRequiredRule.class.php');

class lmbAtleastOneFieldRequiredRuleTest extends lmbValidationRuleTestCase
{
  function testValidSinceFieldIsPresent()
  {
    $dataspace = new lmbSet(array('field1' => 'whatever'));

    $rule = new lmbAtleastOneFieldRequiredRule(array('field1', 'field2'));

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidSinceFieldIsNotPresent()
  {
    $dataspace = new lmbSet();

    $rule = new lmbAtleastOneFieldRequiredRule(array('field1', 'field2'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('Atleast one field required among: {fields}', array('{fields}' => '{0}, {1}'), 'validation'),
                                        array('field1', 'field2'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testValidAndMoreFields()
  {
    $dataspace = new lmbSet(array('field3' => 'whatever'));

    $rule = new lmbAtleastOneFieldRequiredRule(array('field1', 'field2', 'field3'));

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidAndMoreFields()
  {
    $dataspace = new lmbSet();

    $rule = new lmbAtleastOneFieldRequiredRule(array('field1', 'field2', 'field3'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('Atleast one field required among: {fields}', array('{fields}' => '{0}, {1}, {2}'), 'validation'),
                                        array('field1', 'field2', 'field3'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidSinceFieldIsNotPresentWithCustomError()
  {
    $dataspace = new lmbSet();

    $rule = new lmbAtleastOneFieldRequiredRule(array('field1', 'field2'), 'Custom_Error');

    $this->error_list->expectOnce('addError',
                                  array('Custom_Error',
                                        array('field1', 'field2'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }
}


