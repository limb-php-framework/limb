<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMessageBox.
 *
 * @package web_app
 * @version $Id: lmbMessageBox.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbMessageBox
{
  const ERRORS = 1;
  const MESSAGES = 2;

  protected $messages = array();

  function __construct()
  {
    $this->reset();
  }

  function reset()
  {
    $this->messages = array(self :: ERRORS => array(),
                            self :: MESSAGES => array());
  }

  function resetMessages()
  {
    $this->messages[self :: MESSAGES] = array();
  }

  function resetErrors()
  {
    $this->messages[self :: ERRORS] = array();
  }

  function addError($error)
  {
    $this->messages[self :: ERRORS][] = $error;
  }

  function addMessage($message)
  {
    $this->messages[self :: MESSAGES][] = $message;
  }

  function getErrors()
  {
    return $this->messages[self :: ERRORS];
  }

  function getMessages()
  {
    return $this->messages[self :: MESSAGES];
  }

  function hasErrors()
  {
    return sizeof($this->messages[self :: ERRORS]) > 0;
  }

  function hasMessages()
  {
    return sizeof($this->messages[self :: MESSAGES]) > 0;
  }
  
  function getUnifiedList()
  {
    $result = array();
    foreach($this->getErrors() as $error)
      $result[] = array('message' => $error, 'is_error' => true, 'is_message' => false);

    foreach($this->getMessages() as $message)
      $result[] = array('message' => $message, 'is_message' => true, 'is_error' => false);
    
    $this->reset();
    
    return $result;
  }
}


