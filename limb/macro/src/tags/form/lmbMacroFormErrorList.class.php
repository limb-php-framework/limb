<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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

  function setForm(lmbMacroFormWidget $form)
  {
    $this->form = $form;
  }

  function current()
  {
    $error = parent :: current();

    $text = $error['message'];
    
    if(isset($error['fields']))
    {
      $fields = $error['fields'];
      foreach($fields as $key => $fieldName)
      {
        $replacement = '"' . $this->getFieldName($fieldName) . '"';
        $text = str_replace('{' . $key . '}', $replacement, $text);
      }
    }

    if(isset($error['values']))
    {
      $values = $error['values'];
      foreach($values as $key => $replacement)
        $text = str_replace('{' . $key . '}', $replacement, $text);
    }

    $error['message'] = $text;
    return $error;
  }
}

