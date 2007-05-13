<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: error.inc.php 5878 2007-05-13 11:14:57Z serega $
 * @package    wact
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
?>