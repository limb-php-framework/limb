<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/wact/src/components/WactClassPath.class.php');

/**
 * class WactPerformComponent.
 *
 * @package wact
 * @version $Id$
 */
class WactPerformComponent extends WactRuntimeComponent
{
  protected $command;
  protected $method_name = 'perform';
  protected $params = array();
  protected $include_path;

  function setCommand($command)
  {
    $this->command = $command;
  }

  function setIncludePath($path)
  {
    $this->include_path = $path;
  }

  function setMethod($method)
  {
    $this->method_name = $method;
  }

  function addParam($value)
  {
    $this->params[] = $value;
  }

  function process($template)
  {
    $command = $this->_createCommand($template);

    if(!is_a($command, 'WactTemplateCommand'))
      throw new WactException($this->command. '" must inherite from WactTemplateCommand class');

    $method = WactTemplate :: toStudlyCaps('do_' . $this->method_name, false);
    if(!method_exists($command, $method))
      throw new WactException('Template command "' .$this->command. '" does not support method: '. $method);

    return call_user_func_array(array($command, $method), $this->params);
  }

  protected function _createCommand($template)
  {
    $class_path = new WactClassPath($this->command, $this->include_path);
    return $class_path->createObject(array($template, $this->parent));
  }
}

