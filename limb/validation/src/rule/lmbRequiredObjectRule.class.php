<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequiredObjectRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

/**
* Checks that field is present in datasource and it's value is an object of some or any class
* Example of usage:
* <code>
*  lmb_require('limb/validation/src/rule/lmbRequiredObjectRule.class.php');
*  $validator->addRule(new lmbRequiredObjectRule('author', 'Author'));
*  // or
*  $validator->addRequiredObjectRule('folder');
* </code>
* @see lmbValidator :: addRequiredObjectRule()
*/
class lmbRequiredObjectRule implements lmbValidationRule
{
  /**
  * @var string Field name
  */
  protected $field_name;
  /**
  * @var string Required class name
  */
  protected $class;

  /**
  * @param string Field name
  * @param string Required class name
  */
  function __construct($field_name, $class = null)
  {
    $this->field_name = $field_name;
    $this->class = $class;
  }

  /**
  * @see lmbValidationRule :: validate()
  */
  function validate($datasource, $error_list)
  {
    $value = $datasource->get($this->field_name);

    if(!is_object($value) || ($this->class && get_class($value) != $this->class))
    {
      $message = lmb_i18n('Object {Field} is required', 'validation');
      $error_list->addError($message, array('Field' => $this->field_name));
      return;
    }
  }
}
?>