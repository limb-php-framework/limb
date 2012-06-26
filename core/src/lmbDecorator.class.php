<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbReflectionHelper.class.php');

/**
 * class lmbDecoratorGeneratorDefaultEventsHandler
 *
 * @package core
 * @version $Id$
 */
class lmbDecoratorGeneratorDefaultEventsHandler
{
  function onDeclareProperties()
  {
    return "private \$original;";
  }

  function onConstructor()
  {
    return "\$this->original = \$args[0];". PHP_EOL;
  }

  function onMethod($method)
  {
    return "return call_user_func_array(array(\$this->original, '$method'), \$args);". PHP_EOL;
  }

  function onExtra()
  {
    return "";
  }
}

//idea is based on MockGenerator class from SimpleTest test suite
/**
 * class lmbDecorator.
 *
 * @package core
 * @version $Id$
 */
class lmbDecorator
{
  static private $history = array();

  static function generate($decoratee_class, $decorator_class = null, $events_handler = null)
  {
    if($decorator_class == null)
      $decorator_class = $decoratee_class . 'Decorator';

    if(isset(self :: $history[$decorator_class]))
      return false;

    if(class_exists($decorator_class))
      throw new lmbException("Could not generate decorator '$decorator_class' for '$decoratee_class' since there is already conflicting class with the same name");

    if($events_handler == null)
      $events_handler = new lmbDecoratorGeneratorDefaultEventsHandler();

    $res = eval(self :: _createClassCode($decoratee_class, $decorator_class, $events_handler));
    self :: $history[$decorator_class] = 1;
    return true;
  }

  static private function _createClassCode($decoratee_class, $decorator_class, $events_handler)
  {
    $reflection = new ReflectionClass($decoratee_class);
    if($reflection->isInterface())
      $relation = "implements";
    else
      $relation = "extends";

    $code = "class $decorator_class $relation $decoratee_class {". PHP_EOL;

    $code .= $events_handler->onDeclareProperties() . PHP_EOL;
    $code .= "    function __construct() {". PHP_EOL;

    //dealing with proxied class' public properties
    foreach($reflection->getProperties() as $property)
    {
      if($property->isPublic())
        $code .= 'unset($this->' . $property->getName() . ");". PHP_EOL;
    }

    $code .= "        \$args = func_get_args();". PHP_EOL;
    $code .= $events_handler->onConstructor() . PHP_EOL;
    $code .= "    }". PHP_EOL;
    $code .= self :: _createHandlerCode($decoratee_class, $decorator_class, $events_handler) . PHP_EOL;
    $code .= "}". PHP_EOL;
    //var_dump($code);
    return $code;
  }

  static private function _createHandlerCode($decoratee_class, $decorator_class, $events_handler)
  {
    $code = '';
    $methods = lmbReflectionHelper :: getOverridableMethods($decoratee_class);
    foreach($methods as $method)
    {
      if(self :: _isSkipMethod($method))
        continue;

      $code .= "    " . lmbReflectionHelper :: getSignature($decoratee_class, $method) . " {". PHP_EOL;
      $code .= "        \$args = func_get_args();". PHP_EOL;
      $code .= $events_handler->onMethod($method) . PHP_EOL;
      $code .= "    }". PHP_EOL. PHP_EOL;
    }
    $code .= $events_handler->onExtra();
    return $code;
  }

  static private function _isSkipMethod($method)
  {
    return in_array(strtolower($method), array('__construct', '__destruct', '__clone'));
  }
}

