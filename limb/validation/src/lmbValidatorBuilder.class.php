<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/core/src/lmbHandle.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');

lmb_env_setor('LIMB_RULES_INCLUDE_PATH', 'src/rule;limb/*/src/rule;limb/web_app/src/validation/rule');

/**
 * Builds new or fills with the rules existing lmbValidator object, simplifying constructing rules
 * @package validation
 */
class lmbValidatorBuilder
{

  /**
   * @todo correct working of common rules shortcuts, i.e. min shortcut does not work now
   *
   * @var array
   */
  static protected $rules_shortcuts = array(
    'matches' => 'match',
    'not_matches' => 'invalid_value',
    'min_length' => 'size_range',
    'max_length' => 'size_range',
    'range_length' => 'size_range',
    'mb_min_length' => 'i18_n_size_range',
    'mb_max_length' => 'i18_n_size_range',
    'mb_range_length' => 'i18_n_size_range',
    'min' => 'numeric_value_range',
    'max' => 'numeric_value_range',
    'range' => 'numeric_value_range'
  );

  /**
   * Main function for building rules.
   *
   * @param array $rules_lists - list (array) of rules' lists, $field => $list.
   * List of rules can be a string:
   * 	$rules_lists['field'] = 'rule1|rule2|rule3';
   * or an array:
   * 	$rules_lists['field'] = array($rule1, $rule2, $rule3);
   * 
   * Rules can be in several formats:
   * 	$rule['field'] = array(
   * 		'rule1[param1, param2]',  // simple rule format - rule, followed by optional params in square brackets
   * 		'rule2[param1]' => 'error2',  // extended rule format, simple rule format => error message.
   * 		'rule3' => 'error3'
   * 	);  
   * 
   * @param object  $validator  (optional)
   */
  static function addRules($rules_lists, lmbValidator $validator) 
  {
    if(!is_array($rules_lists))
    {
      return;
    }

    foreach($rules_lists as $field => $list)
    {

      if(is_string($list))
      {
        $list = explode('|', $list);
      }

      foreach($list as $rule_name => $rule) // by default $rule has simple format
      {
        $args = '';

        if(is_string($rule_name)) // extended format
        {
          $args = $rule;
          $rule = $rule_name;
        }

        if($object_rule = self :: parseRule($field, $rule, $args))
        {
          $validator->addRule($object_rule);
        }
      }
    }
  }

  /**
  * @return object fully built validator
  */
  static function build($rules_list)
  {
    $validator = new lmbValidator();
    self :: addRules($rules_list, $validator);
    return $validator;
  }

  /**
   * Parse text representation of a rule and return rule object
   *
   * @param string $field
   * @param string $rule
   * @param mixed $args
   * @return object 
   */  
  protected static function parseRule($field, $rule, $args = '') 
  {
    $params = array();

    if(!preg_match('/^([^\[]+)(\[(.+)\])?$/i', $rule, $matches)) // let's parse the rule
    { 
      return null;
    }

    $rule_name = $matches[1];
    
    if(is_array($args) && $args) // args in array overlay args in square brackets
    {
      $params = array_values($args);
    }
    elseif(isset($matches[3]))
    {
      $params = explode(',', $matches[3]);
    }
    
    array_unshift($params, $field); // field must be the first in params

    if(is_string($args) && $args) // if $args is a string, then it's a custom error message
    {
      array_push($params, $args); // and must be the last in the $params 
    }
    
    $params = self :: trim($params);

    $path_to_rule = self :: getPathByRuleName($rule_name);

    return new lmbHandle($path_to_rule, $params);
  }

  static function getPathByRuleName($rule_name) 
  {
    $start_dirs = explode(';', LIMB_RULES_INCLUDE_PATH);
    $rule_file_name = self :: getLmbRule($rule_name);    
    foreach($start_dirs as $dir)
    {      
      $full_path = $dir . '/' . $rule_file_name;
            
      if(lmb_glob($full_path))
      {
        return $full_path;
      }        
    }
    
    throw new lmbException("Rule $rule_name was not found in " . LIMB_RULES_INCLUDE_PATH);    
  }

  static function getLmbRule($underscored_name) 
  {
    if(isset(self :: $rules_shortcuts[$underscored_name]))
    {
      $underscored_name = self :: $rules_shortcuts[$underscored_name];
    }
    
    return 'lmb' . lmb_camel_case($underscored_name) . 'Rule.class.php';
  }

  static function trim($arr) 
  {
    $trimmed = array();
    
    foreach($arr as $key => $value) 
    {
      if(!is_object($value) && !is_array($value))
      {    
        $value = trim($value);
      }        
      
      $trimmed[$key] = $value;
    }

    return $trimmed;
  }
}
