<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMatchRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

/**
* Checks that field value exactly match a reference field value
* Example of usage:
* <code>
*  lmb_require('limb/validation/src/rule/lmbMatchRule.class.php');
*  $validator->addRule(new lmbMatchRule('password', 'repeat_password'));
* </code>
*/
class lmbMatchRule implements lmbValidationRule
{
  /**
  * @var string Reference field name to match against
  */
  protected $reference_field;

  /**
  * @var string Field name to validate
  */
  protected $field_name;

  /**
  * Constructor
  * @param string Field name to validate
  * @param string Reference field name in datasource to match against
  */
  function __construct($field_name, $reference_field)
  {
    $this->field_name = $field_name;
    $this->reference_field = $reference_field;
  }

  /**
  * @see lmbValidationRule :: validate()
  */
  function validate($datasource, $error_list)
  {
    $value1 = $datasource->get($this->field_name);
    $value2 = $datasource->get($this->reference_field);

    if(isset($value1) && isset($value2) && strcmp($value1, $value2))
    {
      $error_list->addError(lmb_i18n('{Field} does not match {MatchField}.', 'validation'),
                  array('Field' => $this->field_name,
                        'MatchField' => $this->reference_field));
    }
  }
}
?>