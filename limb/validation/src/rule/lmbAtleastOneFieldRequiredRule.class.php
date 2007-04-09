<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAtleastOneFieldRequiredRule.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

/**
* Checks that at least one field from a list has not null value
* Example of usage:
* <code>
* lmb_require('limb/validation/src/rule/lmbAtleastOneFieldRequiredRule.class.php');
* $validator->addRule(new lmbAtleastOneFieldRequiredRule('name', 'nickname', 'fullname'));
* </code>
*/
class lmbAtleastOneFieldRequiredRule implements lmbValidationRule
{
  /**
  * @var array List of fields
  */
  protected $field_names;
  /**
  * @var string Custom error message
  */
  protected $custom_error;

  /**
  * Constructor
  * Can accepts any number of arguments. All arguments will be save into $field_names array
  */
  function __construct()
  {
    $args = func_get_args();

    if(is_array($args[0]))
    {
      $this->field_names = $args[0];
      $this->custom_error = isset($args[1]) ? $args[1] : '';
    }
    else
      $this->field_names = $args;
  }

  /**
  * @see lmbValidationRule :: validate()
  */
  function validate($datasource, $error_list)
  {
    if(!$this->_findAtleastOneField($datasource))
    {
      $error = $this->custom_error ? $this->custom_error : $this->_generateErrorMessage();
      $error_list->addError($error, $this->field_names, array());
    }
  }

  protected function _findAtleastOneField($datasource)
  {
    foreach($this->field_names as $field_name)
    {
      if($value = $datasource->get($field_name))
        return true;
    }

    return false;
  }

  protected function _generateErrorMessage()
  {
    for($i = 0; $i < count($this->field_names); $i++)
      $fields[] = '{' . $i . '}';

    return lmb_i18n('Atleast one field required among: {fields}',
                     array('{fields}' => implode(', ', $fields)),
                    'validation');
  }
}
?>