<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorMessage.class.php 5432 2007-03-29 15:36:09Z serega $
 * @package    validation
 */

/**
* Single validation error message.
* Extends ArrayObject just to simplify support for ArrayAccess interface
* Returns result of getErrorMessage() method on any field access
* @see lmbErrorList
*/
class lmbErrorMessage extends ArrayObject
{
  protected $field_list = array();
  protected $values = array();

  /**
  * Constructor.
  * Normally there is no need to initialize objects of lmbErrorMessage in client code since lmbErrorList :: addError() does this
  * @see lmbErrorList :: addError()
  * @param string Error message
  * @param array Array of aliases
  * @param array Array of aliases
  */
  function __construct($message, $field_list = array(), $values = array())
  {
    $this->field_list = $field_list;
    $this->values = $values;

    parent :: __construct(array('message' => $message));
  }

  function getFieldsList()
  {
    return $this->field_list;
  }

  function getValuesList()
  {
    return $this->values;
  }

  function get($name)
  {
    // We should keep BC
    if(in_array($name, array('message', 'ErrorMessage', 'error', 'text')))
      return $this['message'];
    else
      return $this[$name];
  }
}

?>
