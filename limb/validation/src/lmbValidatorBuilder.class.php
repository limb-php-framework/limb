<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/core/src/lmbHandle.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');

@define('LMB_RULES_DIR', 'limb/validation/src/rule');

/**
 * Builds new or fills with the rules existing lmbValidator object, simplifying constructing rules
 * @package validation
 */
class lmbValidatorBuilder {

  /**
   * @todo correct working of common rules shortcuts, i.e. min shortcut does not work now
   *
   * @var array
   */
  static protected $rules_shortcuts = array(  	
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
   * User defined rules, can be filled dynamically using function registerRule
   * 
   * @var array $rule_name => $path
   */
  static protected $user_rules = array();
  
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
   * @return object fully built validator
   */
  static function addRules($rules_lists, $validator = null) {
  	if (!$validator) {
  		$validator = new lmbValidator();
  	}
  	
  	if (!is_array($rules_lists)) {
  		return null; // there must be at least 1 list of rules per field
  	}

  	foreach($rules_lists as $field => $list) {
  		
  		if (is_string($list)) {
  			$list = explode('|', $list);
  		}
  		  		
  		foreach($list as $rule_name => $rule) { // by default $rule has simple format
  			$error = '';
  			
  			if (is_string($rule_name)) { // extended format  				
  				$error = $rule;
  				$rule = $rule_name;  				
  			}  			
  			
  			if ($object_rule = self::parseRule($field, $rule, $error)) {
  				$validator->addRule($object_rule);	
  			}  			
  		}
  	}
  	
  	return $validator;
  }
  
  /**
   * Parse text representation of a rule and return rule object
   *
   * @param string $field
   * @param string $rule
   * @param string $error
   * @return object 
   */  
  protected static function parseRule($field, $rule, $error = '') {
  	
  	$params = array();
  	
  	if (!preg_match('/^([^\[]+)(\[(.+)\])?$/i', $rule, $matches)) { // let's parse the rule
  		return null;
  	}
  	  	
  	$rule_name = $matches[1];
  	if (isset($matches[3])) {
  		$params = explode(',', $matches[3]);  		
  	}
  	
  	array_unshift($params, $field); // field must be the first in params
  	
  	if (!empty($error)) {
  		array_push($params, $error); // but error the last
  	}
  	
  	$params = self::trim($params);
  	
  	$path_to_rule = self::getPathByRuleName($rule_name);
  	
  	return new lmbHandle($path_to_rule, $params);
  }
  
  static function getPathByRuleName($rule_name) {
  	if (isset(self::$user_rules[$rule_name])) {
  		return $user_rules[$rule_name];
  	} elseif (isset(self::$rules_shortcuts[$rule_name])) {
  		return LMB_RULES_DIR . '/' . self::getLmbRule(self::$rules_shortcuts[$rule_name]);
  	} elseif (strpos($rule_name, 'lmb') === 0) { // $rule_name is exactly limb filename
  		return LMB_RULES_DIR . '/' . $rule_name;
  	} else {
  		return LMB_RULES_DIR . '/' . self::getLmbRule($rule_name);
  	}
  }
  
  static function getLmbRule($underscored_name) {
  	return 'lmb' . lmb_camel_case($underscored_name) . 'Rule.class.php';  	
  }
  
  static function registerRule($name, $path) {
  	self::$user_rules[$name] = $path;
  }
  
  static function trim($arr) {  	
  	$trimmed = array();
  	
  	foreach($arr as $key => $value) {
  		$trimmed[$key] = trim($value);
  	}
  	
  	return $trimmed;
  }
}
