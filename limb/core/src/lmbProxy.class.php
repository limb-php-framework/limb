<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Base class for creating proxies.
 * Proxies acts like real(original) objects until real object is definitely required.
 * In such a case the original object is <b>resolved</b> and since that moment all method
 * and argument calls will be delegeted to the real object.
 * @package core
 * @version $Id$
 */
abstract class lmbProxy
{
  /**
  * @var boolean Flag if real object is resolved already
  */
  protected $is_resolved = false;
  /**
  * @var mixed Real object
  */
  protected $original;

  /**
  * Creates original object
  */
  abstract protected function _createOriginalObject();

  /**
  * Resolves original object.
  * Resolving is depend on child classes implementation
  */
  function resolve()
  {
    if($this->is_resolved)
      return $this->original;

    $this->original = $this->_createOriginalObject();
    $this->is_resolved = true;

    return $this->original;
  }

  /**
  * Magic caller
  * Resolves original object and delegates method call to it.
  */
  function __call($method, $args = array())
  {
    $this->resolve();
    if(method_exists($this->original, $method))
      return call_user_func_array(array($this->original, $method), $args);
  }

  /**
  * Magic getter
  * Resolves original object and delegates to it.
  */
  function __get($attr)
  {
    $this->resolve();
    return $this->original->$attr;
  }

  /**
  * Magic setter
  * Resolves original object and delegates to it.
  */
  function __set($attr, $val)
  {
    $this->resolve();
    $this->original->$attr = $val;
  }
}

