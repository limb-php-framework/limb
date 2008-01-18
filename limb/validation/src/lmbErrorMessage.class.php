<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/validation/src/lmbErrorMessage.class.php');

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

  function __toString()
  {
      return $this->getReadable();
  }
}
