<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollection.class.php');

/**
 * Holds a list of validation errors
 * @see lmbErrorMessage
 * @package validation
 * @version $Id: lmbErrorList.class.php 6639 2007-12-25 09:01:29Z serega $
 */
class lmbErrorList extends lmbCollection
{
  /**
  * Adds new error.
  * Creates an object of {@link lmbErrorMessage} class.
  * Accepts error message, array of fields list which this error is belong to and array of values.
  * Error message can contain placeholders like {Placeholder} that will be replaced with field names
  * and values in {@link lmbErrorMessage :: getReadable()}
  * Here is an example of adding error to error list in some validation rule:
  * <code>
  *  $error_list->addError('{Field} must contain at least {min} characters.', array('Field' => 'password'), array('min' => 5));
  * </code>
  * After all replacements we can get something like "password must contain at least 5 characters"
  * @param string Error message with placeholders like {Field} must contain at least {min} characters.
  * @param array Array of aliases and field names like array('BaseField' => 'password', 'RepeatField' => 'repeat_password')
  * @param array Array of aliases and field values like array('Min' => 5, 'Max' => 15)
  * @return lmbErrorMessage
  */
  function addError($message, $fields = array(), $values = array())
  {
    $error = array('message' => $message, 'fields' => $fields, 'values' => $values);
    $this->add($error);
    return $error;
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
  * Return processed error list with formatted messages
  * @see lmbErrorList :: addError()
  * @see __construct
  * @return string
  */
  function getReadable()
  {
    $result = array();

    foreach($this as $error)
    {
      $text = $error['message'];

      foreach($error['fields'] as $key => $fieldName)
      {
        $replacement = '"' . $fieldName . '"';
        $text = str_replace('{' . $key . '}', $replacement, $text);
      }

      foreach($error['values'] as $key => $replacement)
        $text = str_replace('{' . $key . '}', $replacement, $text);

      $result[] = $text;
    }
    return $result;
  }
}
