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
lmb_require('limb/core/src/lmbDecoratorGenerator.class.php');

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

  protected function ___invoke($method, $args = array())
  {
    return call_user_func_array(array($this->original, $method), $args);
  }
}
?>
