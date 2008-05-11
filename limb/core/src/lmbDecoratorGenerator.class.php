<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
//code is based on MockGenerator class from SimpleTest test suite
lmb_require('limb/core/src/lmbReflectionHelper.class.php');

/**
 * class lmbDecoratorGenerator.
 *
 * @package core
 * @version $Id$
 */
class lmbDecoratorGenerator
{
  protected $_class;
  protected $_decorator_class;
  protected $_decorator_base;

  function generate($class, $decorator_class = null, $decorator_base = 'lmbDecorator')
  {
    $this->_class = $class;

    if(is_null($decorator_class))
      $this->_decorator_class = $class . 'Decorator';
    else
      $this->_decorator_class = $decorator_class;

    $this->_decorator_base = $decorator_base;

    if(class_exists($this->_decorator_class))
      return false;

    $methods = array();

    return eval($this->_createClassCode() . " return true;");
  }

  protected function _createClassCode()
  {
    $implements = '';
    $interfaces = lmbReflectionHelper :: getInterfaces($this->_class);
    if(function_exists('spl_classes'))
      $interfaces = array_diff($interfaces, array('Traversable'));

    if(count($interfaces) > 0)
      $implements = 'implements ' . implode(', ', $interfaces);

    $code = "class " . $this->_decorator_class . " extends " . $this->_decorator_base . " $implements {\n";
    $code .= "    function __construct(\$original) {\n";
    $code .= "        parent :: __construct(\$original);\n";
    $code .= "    }\n";
    $code .= $this->_createHandlerCode();
    $code .= "}\n";
    return $code;
  }

  protected function _createHandlerCode()
  {
    $code = '';
    $methods = lmbReflectionHelper :: getMethods($this->_class);
    $base_methods = lmbReflectionHelper :: getMethods($this->_decorator_base);
    foreach($methods as $method)
    {
      if($this->_isMagicMethod($method))
        continue;

      if(in_array($method, $base_methods))
        continue;

      $code .= "    " . lmbReflectionHelper :: getSignature($this->_class, $method) . " {\n";
      $code .= "        \$args = func_get_args();\n";
      $code .= "        return \$this->__call(\"$method\", \$args);\n";
      $code .= "    }\n";
    }
    return $code;
  }

  protected function _isMagicMethod($method)
  {
    return in_array(strtolower($method), array('__construct', '__destruct', '__clone'));
  }
}

