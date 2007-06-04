<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

abstract class lmbView
{
  protected $template_name;
  protected $variables = array();

  function __construct($template_name = '')
  {
    $this->template_name = $template_name;
  }

  function setTemplate($template_name)
  {
    $this->template_name = $template_name;
  }

  function hasTemplate()
  {
    return $this->template_name != '';
  }

  abstract function render();

  function reset()
  {
    $this->variables = array();
  }

  function getTemplate()
  {
    return $this->template_name;
  }

  function set($variable_name, $value)
  {
    $this->variables[$variable_name] = $value;
  }

  function setVariables($vars)
  {
    $this->variables = $vars;
  }

  function get($variable_name)
  {
    if(isset($this->variables[$variable_name]))
      return $this->variables[$variable_name];
  }

  function getVariables()
  {
    return $this->variables;
  }
}
?>
