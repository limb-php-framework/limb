<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUrlRuleTest.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbUrlRule.class.php');

class lmbUrlRuleTest extends lmbValidationRuleTestCase
{
  function testUrlRule()
  {
    $rule = new lmbUrlRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'http://www.sourceforge.net/');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testUrlRuleBadScheme()
  {
    $allowedSchemes = array('http');
    $rule = new lmbUrlRule('testfield',$allowedSchemes);

    $dataspace = new lmbDataspace();
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

    $dataspace = new lmbDataspace();
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

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'http://www.source--forge.net/');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} may not contain double hyphens (--).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }
}

?>