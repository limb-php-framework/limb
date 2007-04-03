<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbValidationException.class.php 5222 2007-03-13 13:26:27Z serega $
 * @package    validation
 */
lmb_require('limb/core/src/exception/lmbException.class.php');

/**
* Validation exception.
* Uses in some classes where validation process is very important for performing an operation successfully
* @see lmbActiveRecord :: save()
*/
class lmbValidationException extends lmbException
{
  /**
  * @var lmbErrorList
  */
  protected $error_list;

  /**
  * Constructor
  * @param string Exception message
  * @param lmbErrorList List of validation errors
  * @param array List of extra exception params
  * @param int Exception code
  */
  function __construct($message, $error_list, $params = array(), $code = 0)
  {
    $this->error_list = $error_list;

    $errors = array();
    foreach($this->error_list as $error)
      $errors[] .= $error->get('message');

    $error_text = implode(', ', $errors);

    $params['errors_list'] = $error_text;

    $message .= ' Errors list : ' . $error_text;

    parent :: __construct($message, $params, $code);
  }

  function getErrorList()
  {
    return $this->error_list;
  }
}

?>