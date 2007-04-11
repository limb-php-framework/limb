<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmailRuleTest.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
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

?>