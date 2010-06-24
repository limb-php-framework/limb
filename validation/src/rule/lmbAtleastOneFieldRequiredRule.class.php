<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbBaseValidationRule.class.php');

/**
 * Checks that at least one field from a list has not null value
 * Example of usage:
 * <code>
 * lmb_require('limb/validation/src/rule/lmbAtleastOneFieldRequiredRule.class.php');
 * $validator->addRule(new lmbAtleastOneFieldRequiredRule(array('name', 'nickname', 'fullname')));
 * </code>
 * @package validation
 * @version $Id: lmbAtleastOneFieldRequiredRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbAtleastOneFieldRequiredRule extends lmbBaseValidationRule
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
  * @see lmbBaseValidationRule :: _doValidate()
  */
  protected function _doValidate($datasource)
  {
    if(!$this->_findAtleastOneField($datasource))
    {
      $error = $this->custom_error ? $this->custom_error : $this->_generateErrorMessage();
      $this->error($error, $this->field_names, array());
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

