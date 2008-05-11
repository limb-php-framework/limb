<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbDecoratorGenerator.class.php');

/**
 * class lmbDecorator.
 *
 * @package core
 * @version $Id$
 */
class lmbDecorator
{
  protected $original;

  static function generate($class, $decorator_class = null)
  {
    $generator = new lmbDecoratorGenerator();
    return $generator->generate($class, $decorator_class);
  }

  function __construct($original)
  {
    $this->original = $original;
  }

  function __call($method, $args = array())
  {
    return call_user_func_array(array($this->original, $method), $args);
  }
}

