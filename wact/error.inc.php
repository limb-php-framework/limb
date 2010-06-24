<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactException.
 *
 * @package wact
 * @version $Id: error.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactException extends Exception
{
  protected $message;
  protected $params;

  function __construct($message, $params = NULL)
  {
    $this->message = $message;
    if(!is_array($params))
      $this->params = array();
    else
      $this->params = $params;

    $error_message = 'WACT exception: ' . $message . "\n. Params : " . var_export($this->params, true);

    parent :: __construct($error_message);
  }

  function getParams()
  {
    return $this->params;
  }

  function getParam($name)
  {
    if(isset($this->params[$name]))
      return $this->params[$name];
  }
}

