<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');

class lmbDomainRuleTest extends lmbValidationRuleTestCase
{
  function testDomainRule()
  {
    $rule = new lmbDomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => 'sourceforge.net'));

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleBlank()
  {
    $rule = new lmbDomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => ''));

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleBadCharacters()
  {
    $rule = new lmbDomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => 'source#&%forge.net'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must contain only letters, numbers, hyphens, and periods.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleDoubleHyphens()
  {
    $rule = new lmbDomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => 'source--forge.net'));

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} may not contain double hyphens (--).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleTooLarge()
  {
    $rule = new lmbDomainRule('testfield');

    $segment = "abcdefg-hijklmnop-qrs-tuv-wx-yz-ABCDEFG-HIJKLMNOP-QRS-TUV-WX-YZ-0123456789";

    $dataspace = new lmbSet();
    $dataspace->set('testfield', $segment . '.net');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} segment {segment} is too large (it must be 63 characters or less).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('segment'=>$segment)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainHyphenBegin()
  {
    $rule = new lmbDomainRule('testfield');

    $segment = "-sourceforge";

    $dataspace = new lmbSet();
    $dataspace->set('testfield', $segment . '.net');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} segment {segment} may not begin or end with a hyphen.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('segment'=>$segment)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleHyphenEnd()
  {
    $rule = new lmbDomainRule('testfield');

    $segment = "sourceforge-";

    $dataspace = new lmbSet();
    $dataspace->set('testfield', $segment . '.net');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} segment {segment} may not begin or end with a hyphen.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('segment'=>$segment)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleCombination()
  {
    $rule = new lmbDomainRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '.n..aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.');

    $this->error_list->expectCallCount('addError', 4);

    $this->error_list->expectArgumentsAt(0,
                                        'addError',
                                         array(lmb_i18n('{Field} cannot start with a period.', 'validation'),
                                               array('Field'=>'testfield'),
                                               array()));

    $this->error_list->expectArgumentsAt(1,
                                        'addError',
                                         array(lmb_i18n('{Field} cannot end with a period.', 'validation'),
                                               array('Field'=>'testfield'),
                                               array()));

    $this->error_list->expectArgumentsAt(2,
                                        'addError',
                                         array(lmb_i18n('{Field} may not contain double periods (..).', 'validation'),
                                               array('Field'=>'testfield'),
                                               array()));

    $this->error_list->expectArgumentsAt(3,
                                        'addError',
                                        array(lmb_i18n('{Field} segment {segment} is too large (it must be 63 characters or less).', 'validation'),
                                              array('Field'=>'testfield'),
                                              array('segment' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleDoubleDomain()
  {
    $rule = new lmbDomainRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'microsoft.co.uk');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleLocalDomain()
  {
    $rule = new lmbDomainRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'localhost');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }
}

