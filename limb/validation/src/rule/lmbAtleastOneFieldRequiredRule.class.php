<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

/**
 * Checks that at least one field from a list has not null value
 * Example of usage:
 * <code>
 * lmb_require('limb/validation/src/rule/lmbAtleastOneFieldRequiredRule.class.php');
 * $validator->addRule(new lmbAtleastOneFieldRequiredRule(array('name', 'nickname', 'fullname')));
 * </code>
 * @package validation
 * @version $Id: lmbAtleastOneFieldRequiredRule.class.php 5945 2007-06-06 08:31:43Z pachanga $
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

  function __construct($field_names, $custom_error = '')
  {
    $this->field_names = $field_names;
    $this->custom_error = $custom_error;
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