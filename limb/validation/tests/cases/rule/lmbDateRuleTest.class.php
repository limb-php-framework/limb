<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbDateRule.class.php');

class lmbDateRuleTest extends lmbValidationRuleTestCase
{
  function testValidForISO()
  {
    $rule = new lmbDateRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '2007-01-12 12:30');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidForISO()
  {
    $rule = new lmbDateRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'blah 12:30');

    $this->error_list->expectOnce('addError');

    $rule->validate($dataspace, $this->error_list);
  }

}


