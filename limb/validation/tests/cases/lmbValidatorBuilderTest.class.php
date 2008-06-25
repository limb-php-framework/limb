<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/validation/src/lmbValidatorBuilder.class.php');

Mock::generate('lmbValidator', 'MockValidator');

class lmbValidatorBuilderTest extends UnitTestCase
{
  var $validator;

  function setUp()
  {
    $this->validator = new MockValidator();
  }

  function testAddRulesFromSimpleString()
  {
    $rules = array("login" => "required"); 
    $this->validator->expectOnce("addRule", array(new EqualExpectation(new lmbHandle('limb/validation/src/rule/lmbRequiredRule.class.php', array('login')))));
    lmbValidatorBuilder :: addRules($rules, $this->validator);
  }
}


