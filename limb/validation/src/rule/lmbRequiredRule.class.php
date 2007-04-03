<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequiredRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */

lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

/**
* Checks that field is present in datasource and has not empty value
* Example of usage:
* <code>
*  lmb_require('limb/validation/src/rule/lmbRequiredRule.class.php');
*  $validator->addRule(new lmbRequiredRule('title'));
*  //or
*  $validator->addRule(new lmbHandle('limb/validation/src/rule/lmbRequiredRule', array('title')));
*  // or
*  $validator->addRequiredRule('title');
* </code>
* @see lmbValidator :: addRequiredRule()
*/
class lmbRequiredRule implements lmbValidationRule
{
  /**
  * @var string Field name
  */
  protected $field_name;

  /**
  * Constructor
  * @param string Field name
  */
  function __construct($field_name)
  {
    $this->field_name = $field_name;
  }

  /**
  * @see lmbValidationRule :: validate()
  */
  function validate($datasource, $error_list)
  {
    $value = $datasource->get($this->field_name);
    if(is_null($value) || $value === '')
    {
      $message = lmb_i18n('{Field} is required', 'validation');
      $error_list->addError($message, array('Field' => $this->field_name));
      return;
    }
  }
}
?>