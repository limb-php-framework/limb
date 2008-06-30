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
 * Single validation error message.
 * @package validation
 * @version $Id$
 */
class lmbErrorMessage extends lmbObject  
{  
  function __construct($message, $fields = array(), $values = array())  
  {  
     parent::__construct(array('message' => $message, 'fields' => $fields, 'values' => $values));
  }

  function getReadable()  
  {   
    $text = $this->getMessage();
    foreach($this->getFields() as $key => $fieldName)
    {
      $replacement = '"' . $fieldName . '"';
      $text = str_replace('{' . $key . '}', $replacement, $text);
    }

    foreach($this->getValues() as $key => $replacement)
      $text = str_replace('{' . $key . '}', $replacement, $text);

    return $text;
  }  

  function renameFields($new_field_names) 
  {
    
    if(!is_array($new_field_names)) 
    {
      return;
    }
      
    $new_fields = str_replace(array_keys($new_field_names), $new_field_names, $this->getFields());
    $this->setFields($new_fields);   
  }
  
  function __toString()
  {
      return $this->getReadable();
  }
}
