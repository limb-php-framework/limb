<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
    $rules['login'] = "required|matches[bbb]|size_range[5, 8]|identifier";
    
    $calls_counter = 0;
    
    // no params
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbRequiredRule.class.php', array('login'))
        )
      )
    );
    
    // 1 param
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbMatchRule.class.php', array('login', 'bbb'))
        )
      )
    );
    
    // 2 (or more) params
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbSizeRangeRule.class.php', array('login', 5, 8))
        )
      )
    );
    
    // rule name is exactly matches to file name
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbIdentifierRule.class.php', array('login'))
        )
      )
    );
        
    lmbValidatorBuilder :: addRules($rules, $this->validator);
  }
    
  function testAddRulesFromArrayWithCustomArguments()
  {
    $errors = array(
      'email' => 'Email error',
      'pattern' => 'Not a digit',
      'size_range' => 'Size range error!'  
    );
        
    $rules = array();
    
    $rules['login'] = array(
      'required',
      'size_range[5, 8]',
      'email' => $errors['email'],
      'pattern[/\d+/]' => $errors['pattern'],
      'size_range[5, 8]' => array( // params [5, 8] will be ignored because of args have array type
        'min' => 10,
        'max' => 15,
        'error' => $errors['size_range']  // keys (min, max, error) are ignored, the order of args is still important
      )
    );
    
    $calls_counter = 0;
    
    // no params
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbRequiredRule.class.php', array('login'))
        )
      )
    );

   $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbSizeRangeRule.class.php', array('login', 5, 8))
        )
      )
    );
    
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbEmailRule.class.php', array('login', $errors['email']))
        )
      )
    );
        
    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbPatternRule.class.php', array('login', '/\d+/', $errors['pattern']))
        )
      )
    );
        
   $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/*/src/rule/lmbSizeRangeRule.class.php', array('login', 10, 15, $errors['size_range']))
        )
      )
    );
    
        
    lmbValidatorBuilder :: addRules($rules, $this->validator);
  }
  
  function testAddCustomRules()
  {         
    $rules = array();
    $rules['login'] = array(
      "unique_table_field" => array(
        'table' => 'user',
        'field' => 'login'
      )
    );
    
    $calls_counter = 0;

    $this->validator->expectAt(
      $calls_counter++,
      "addRule", 
      array(
        new EqualExpectation(
          new lmbHandle('limb/web_app/src/validation/rule/lmbUniqueTableFieldRule.class.php', array('login', 'user', 'login'))
        )
      )
    );
    
    lmbValidatorBuilder :: addRules($rules, $this->validator);    
  }
}
