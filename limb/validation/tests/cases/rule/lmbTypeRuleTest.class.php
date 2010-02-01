<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbTypeRule.class.php');

class lmbTypeRuleTest extends lmbValidationRuleTestCase
{
  function testPositive()
  {
    $column = 'field';
    $rule = new lmbTypeRule($column, 'double');

    $dataspace = new lmbSet(array($column => 1.1));

    $this->error_list->expectNever('addError');
    $rule->validate($dataspace, $this->error_list);
  }

  function testNegative()
  {
    $column = 'field';
    $rule = new lmbTypeRule($column, 'double');

    $dataspace = new lmbSet(array($column => 1));

    $this->error_list->expectOnce('addError');
    $rule->validate($dataspace, $this->error_list);
  }

  function testCustomError()
  {
    $column = 'field';
    $rule = new lmbTypeRule($column, 'double', '{Field} error');

    $dataspace = new lmbSet(array($column => 1));
    $error_list = new lmbErrorList();
    $rule->validate($dataspace, $error_list);

    $this->assertEqual(current($error_list->getReadable()), '"field" error');
  }
}

