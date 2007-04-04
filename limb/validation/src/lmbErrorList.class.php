<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorList.class.php 5528 2007-04-04 15:08:50Z pachanga $
 * @package    validation
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');
lmb_require('limb/validation/src/lmbErrorMessage.class.php');
lmb_require('limb/classkit/src/lmbObject.class.php');

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
  * Adds new error.
  * Creates an object of {@link lmbErrorMessage} class.
  * Accepts error message, array of fields list which this error is belong to and array of values.
  * Error message can contain placeholders like {Placeholder} that will be replaced with field names
  * and values in {@link lmbErrorMessage :: getReadableErrorList()}
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
  function addError($message, $fields = array(), $values = array())
  {
    $error = new lmbObject(array('error' => $message,
                                 'message' => $message,//duplicating for BC
                                 'fields' => $fields,
                                 'values' => $values));
    $this->add($error);
    return $error;
  }

  /**
  * Returns humal readable name of a field
  * Actually delegates to field name dictionary
  * @param string
  * @return string
  */
  function getFieldName($field)
  {
    if(is_object($this->field_name_dictionary))
      return $this->field_name_dictionary->getFieldName($field);
    else
      return $field;
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
  * Return processed error list with translated and formatted messages
  * @see lmbErrorList :: addError()
  * @see __construct
  * @return string
  */
  function getReadable()
  {
    $new_list = new lmbErrorList();

    foreach($this as $error)
    {
      $text = $error->getMessage();

      foreach($error->getFields() as $key => $fieldName)
      {
        $replacement = $this->getFieldName($fieldName);
        $text = str_replace('{' . $key . '}', $replacement, $text);
      }

      foreach($error->getValues() as $key => $replacement)
        $text = str_replace('{' . $key . '}', $replacement, $text);

      $new_list->addError($text, $error->getFields(), $error->getValues());
    }
    return $new_list;
  }
}
