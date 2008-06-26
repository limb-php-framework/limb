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
    $rules = array();
    $rules['login'] = "required|matches[bbb]|size_range[5, 8]|lmbIdentifierRule.class.php";
    
    $calls_counter = 0;
    
    // no params
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbRequiredRule.class.php', array('login'))
        )
      )
    );
    
    // 1 param
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbMatchRule.class.php', array('login', 'bbb'))
        )
      )
    );
    
    // 2 (or more) params
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbSizeRangeRule.class.php', array('login', 5, 8))
        )
      )
    );
    
    // rule name is exactly matches to file name
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbIdentifierRule.class.php', array('login'))
        )
      )
    );
        
    lmbValidatorBuilder :: addRules($rules, $this->validator);
  }
    
  function testAddRulesFromArrayWithCustomError()
  {
    $errors = array(
      'email' => 'Email error',
      'pattern' => 'Not a digit'    
    );
        
    $rules = array();
    
    $rules['login'] = array(
      'required',
      'size_range[5, 8]',
      'email' => $errors['email'],
      'pattern[/\d+/]' => $errors['pattern'],
    );
    
    $calls_counter = 0;
    
    // no params
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbRequiredRule.class.php', array('login'))
        )
      )
    );

   $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbSizeRangeRule.class.php', array('login', 5, 8))
        )
      )
    );
    
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbEmailRule.class.php', array('login', $errors['email']))
        )
      )
    );
        
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/validation/src/rule/lmbPatternRule.class.php', array('login', '/\d+/', $errors['pattern']))
        )
      )
    );
        
    lmbValidatorBuilder :: addRules($rules, $this->validator);
  }
}
