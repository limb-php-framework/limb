<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Form error list container
 * @package macro
 * @version $Id$
 */
class lmbMacroFormErrorList extends ArrayIterator
{
  protected $form;
  
  function addError($message, $fields = array(), $values = array())
  {
    $this->append(array('message' => $message, 'fields' => $fields, 'values' => $values));
  }

  function getFieldName($field_id)
  {
    if(!$this->form)
      return $field_id;
    
    $form_field = $this->form->getChild($field_id);
    if (is_object($form_field))
      return $form_field->getDisplayName();
    else
      return $field_id;
  }

  function bindToForm($form)
  {
    $this->form = $form;
    $form->setErrorList($this);
  }

  function current()
  {
    $error = parent :: current();

    $text = $error['message'];
    
    foreach($error['fields'] as $key => $fieldName)
    {
      $replacement = $this->getFieldName($fieldName);
      $text = str_replace('{' . $key . '}', $replacement, $text);
    }

    foreach($error['values'] as $key => $replacement)
      $text = str_replace('{' . $key . '}', $replacement, $text);

    $error['message'] = $text;
    return $error;
  }
}

