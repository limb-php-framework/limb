<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: error.inc.php 5168 2007-02-28 16:05:08Z serega $
 * @package    wact
 */

/**
* Translates between form name attributes and tag displayname
* attributes (human reabable).
*/
class WactFormFieldNameDictionary
{

  /**
  * @var WactFormComponent
  */
  protected $form;

  function __construct($form)
  {
    $this->form = $form;
  }

  /**
  * @param string name attribute of the field
  * @return string displayname attribute of the field
  * @access protected
  */
  function getFieldName($field_name)
  {
    $field = $this->form->findChild($field_name);
    if (is_object($field))
      return $field->getDisplayName();
    else
      return $field_name;
  }
}

class WactFormErrorList extends ArrayIterator
{
  protected $field_name_dictionary;

  function addError($message, $fields = array(), $values = array())
  {
    $this->append(new WactFormError($message, $fields, $values));
  }

  function setFieldNameDictionary($dict)
  {
    $this->field_name_dictionary = $dict;
  }

  function current()
  {
    $error = parent :: current();

    $text = $error['message'];
    foreach($error->getFieldsList() as $key => $fieldName)
    {
      $replacement = $this->field_name_dictionary->getFieldName($fieldName);
      $text = str_replace('{' . $key . '}', $replacement, $text);
    }

    foreach($error->getValuesList() as $key => $replacement)
      $text = str_replace('{' . $key . '}', $replacement, $text);

    $error['message'] = $text;
    return $error;
  }
}

class WactFormError extends ArrayObject
{
  protected $fields_list = array();
  protected $values = array();

  function __construct($message, $fields_list = array(), $values = array())
  {
    parent :: __construct(array('message' => $message));

    $this->fields_list = $fields_list;
    $this->values = $values;
  }

  function getFieldsList()
  {
    return $this->fields_list;
  }

  function getValuesList()
  {
    return $this->values;
  }
}
?>