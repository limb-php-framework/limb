<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDecoratorGenerator.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
//code is based on MockGenerator class from SimpleTest test suite
lmb_require('limb/classkit/src/lmbReflection.class.php');

class lmbDecoratorGenerator
{
  protected $_class;
  protected $_decorator_class;
  protected $_decorator_base;
  protected $_reflection;

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

    $this->_reflection = new lmbReflection($this->_class);

    $methods = array();

    return eval($this->_createClassCode() . " return true;");
  }

  protected function _createClassCode()
  {
    $implements = '';
    $interfaces = $this->_reflection->getInterfaces();
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
    $methods = $this->_reflection->getMethods();
    $base_reflection = new lmbReflection($this->_decorator_base);
    foreach($methods as $method)
    {
      if($this->_isMagicMethod($method))
        continue;

      if(in_array($method, $base_reflection->getMethods()))
        continue;

      $code .= "    " . $this->_reflection->getSignature($method) . " {\n";
      $code .= "        \$args = func_get_args();\n";
      $code .= "        return \$this->___invoke(\"$method\", \$args);\n";
      $code .= "    }\n";
    }
    return $code;
  }

  protected function _isMagicMethod($method)
  {
    return in_array(strtolower($method), array('__construct', '__destruct', '__clone'));
  }
}
?>
