<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorList.class.php 5222 2007-03-13 13:26:27Z serega $
 * @package    validation
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');
lmb_require('limb/validation/src/lmbErrorMessage.class.php');

/**
* Holds a list of validation errors
* @see lmbErrorMessage
*/
class lmbErrorList extends lmbArrayDataset
{
  /**
  * @see lmbErrorMessage :: getErrorMessage()
  * @see getFieldName()
  * @var object Field name dictionary that is used in making human readable error messages
  */
  protected $field_name_dictionary;

  /**
  * Sets new field name dictionary
  * Usually this happens in {@link WactFormComponent :: setErrors()}
  * @see WactFormFieldNameDictionary
  * @param mixed New field name dictionary object.
  * @return void
  */
  function setFieldNameDictionary($dictionary)
  {
    $this->field_name_dictionary = $dictionary;
  }

  /**
  * Returns field name dictionary
  * Creates a new {@link lmbDefaultFieldNameDictionary} if dictionary is NULL and returns it
  * @return mixed current field name dictionary or just created {@link lmbDefaultFieldNameDictionary}
  */
  function getFieldNameDictionary()
  {
    if(!$this->field_name_dictionary)
    {
      lmb_require('limb/validation/src/lmbDefaultFieldNameDictionary.class.php');
      $this->setFieldNameDictionary(new lmbDefaultFieldNameDictionary());
    }
    return $this->field_name_dictionary;
  }

  /**
  * Adds new error.
  * Creates an object of {@link lmbErrorMessage} class.
  * Accepts error message, array of fields list which this error is belong to and array of values.
  * Error message can contain placeholders like {Placeholder} that will be replaced with field names
  * and values in {@link lmbErrorMessage :: getErrorMessage()}
  * Here is an example of adding error to error list in some validation rule:
  * <code>
  *  $error_list->addError('{Field} must contain at least {min} characters.', array('Field' => 'password'), array('min' => 5));
  * </code>
  * After all replacements we can get something like "Password must contain at least 5 characters", there "password" becomes "Password"
  * @param string Error message with placeholders like {Field} must contain at least {min} characters.
  * @param array Array of aliases and field names like array('BaseField' => 'password', 'RepeatField' => 'repeat_password')
  * @param array Array of aliases and field values like array('Min' => 5, 'Max' => 15)
  * @return lmbErrorMessage
  */
  function addError($message, $field_list = array(), $values = array())
  {
    $error = new lmbErrorMessage($message, $field_list, $values);
    $this->add($error);
    return $error;
  }

  /**
  * Returns humal readable name of a field
  * Actually delegates to field name dictionary
  * @param string
  * @return string
  */
  function getFieldName($field_name)
  {
    return $this->getFieldNameDictionary()->getFieldName($field_name);
  }

  /**
  * Returns FALSE is contains at least one error, otherwise returns TRUE
  * @return boolean
  */
  function isValid()
  {
    return $this->isEmpty();
  }

  /**
  * Return processed error message
  * Replaces placeholders in {@link $message} with values from {@link $field_list} and {@link $values}
  * @see lmbErrorList :: addError()
  * @see lmbErrorList :: getFieldName()
  * @see __construct
  * @return string
  */
  function current()
  {
    $error = parent :: current();

    $text = $error['message'];
    foreach($error->getFieldsList() as $key => $fieldName)
    {
      $replacement = $this->getFieldName($fieldName);
      $text = str_replace('{' . $key . '}', $replacement, $text);
    }

    foreach($error->getValuesList() as $key => $replacement)
      $text = str_replace('{' . $key . '}', $replacement, $text);

    $error['message'] = $text;
    return $error;
  }
}
