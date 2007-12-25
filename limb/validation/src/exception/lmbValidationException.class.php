<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/exception/lmbException.class.php');

/**
 * Validation exception.
 * Uses in some classes where validation process is very important for performing an operation successfully
 * @see lmbActiveRecord :: save()
 * @package validation
 * @version $Id: lmbValidationException.class.php 6639 2007-12-25 09:01:29Z serega $
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
    $this->error_list = $error_list->getReadable();

    $message .= ' Errors list : ' . implode(', ', $this->error_list);

    parent :: __construct($message, $params, $code);
  }

  function getErrorList()
  {
    return $this->error_list;
  }
}


