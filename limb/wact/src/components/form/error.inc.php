<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Translates between form name attributes and tag displayname
 * attributes (human reabable).
 * @package wact
 * @version $Id: error.inc.php 6802 2008-02-18 21:05:10Z 3dmax $
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
  * @package wact
 * @version $Id: error.inc.php 6802 2008-02-18 21:05:10Z 3dmax $
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
    $this->append(array('message' => $message, 'fields' => $fields, 'values' => $values));
  }

  function setFieldNameDictionary($dict)
  {
    $this->field_name_dictionary = $dict;
  }

  function getFieldName($field)
  {
    if(is_object($this->field_name_dictionary))
      return $this->field_name_dictionary->getFieldName($field);
    else
      return $field;
  }

  function bindToForm($form)
  {
    $this->setFieldNameDictionary(new WactFormFieldNameDictionary($form));
    $form->setErrors($this);
  }

  function current()
  {
    $error = parent :: current();

    $text = $error['message'];

    $error_fields = $error['fields'];
    foreach($error_fields as $key => $fieldName)
    {
      $replacement = '"' . $this->getFieldName($fieldName) . '"';
      $text = str_replace('{' . $key . '}', $replacement, $text);
    }

    $error_values = $error['values'];
    foreach($error_values as $key => $replacement)
      $text = str_replace('{' . $key . '}', $replacement, $text);

    $error['message'] = $text;
    return $error;
  }
}
