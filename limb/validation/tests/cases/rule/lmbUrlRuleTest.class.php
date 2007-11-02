<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbUrlRule.class.php');

class lmbUrlRuleTest extends lmbValidationRuleTestCase
{
  function testUrlRule()
  {
    $rule = new lmbUrlRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'http://www.sourceforge.net/');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }
  
  function testUrlRuleWithoutSchema()
  {
    $rule = new lmbUrlRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'www.sourceforge.net/');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }
  
  function testUrlRuleBadScheme()
  {
    $allowedSchemes = array('http');
    $rule = new lmbUrlRule('testfield',$allowedSchemes);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'ftp://www.sourceforge.net/');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} may not use {scheme}.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('scheme'=>'ftp')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testUrlRuleMissingScheme()
  {
    $allowedSchemes = array('http');
    $rule = new lmbUrlRule('testfield',$allowedSchemes);

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'www.sourceforge.net/');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('Please specify a scheme for {Field}.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testUrlRuleDomain()
  {
    $rule = new lmbUrlRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'http://www.source--forge.net/');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} may not contain double hyphens (--).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testUrlRuleDomainWithCustomError()
  {
    $rule = new lmbUrlRule('testfield', '', 'Custom_Error');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'http://www.source--forge.net/');

    $this->error_list->expectOnce('addError',
                                  array('Custom_Error',
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testUrlRuleBadSchemeWithCustomError()
  {
    $allowedSchemes = array('http');
    $rule = new lmbUrlRule('testfield', $allowedSchemes, 'Custom_error');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'ftp://www.sourceforge.net/');

    $this->error_list->expectOnce('addError',
                                  array('Custom_error',
                                        array('Field'=>'testfield'),
                                        array('scheme'=>'ftp')));

    $rule->validate($dataspace, $this->error_list);
  }
}


