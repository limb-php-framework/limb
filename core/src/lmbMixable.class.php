<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMixable.
 *
 * @package core
 * @version $Id$
 */
class lmbMixable
{
  protected $owner;
  protected $mixins = array();
  protected $mixins_loaded = false;
  protected $mixins_signatures = array();

  function mixin($mixin)
  {
    $this->mixins[] = $mixin;
  }

  function setOwner($owner)
  {
    $this->owner = $owner;
  }

  function __call($method, $args)
  {
    $this->_ensureSignatures();

    if(!isset($this->mixins_signatures[$method]))
      throw new lmbException('Mixable does not support method "' . $method . '" (no such signature)',
                              array('method' => $method));


    return call_user_func_array(array($this->mixins_signatures[$method], $method), $args);
  }

  function _get($name)
  {
    if(isset($this->$name))
      return $this->$name;
  }

  protected function _ensureSignatures()
  {
    if($this->mixins_loaded)
      return;

    $owner = $this->owner ? $this->owner : $this;

    foreach($this->mixins as $mixin)
    {
      if(is_object($mixin))
      {
        $obj = $mixin;
        $class = get_class($mixin);
      }
      else
      {
        $obj = new $mixin();
        $class = $mixin;
      }
      $obj->setOwner($owner);

      foreach(get_class_methods($class) as $method)
        $this->mixins_signatures[$method] = $obj;
    }

    $this->mixins_loaded = true;
  }
}

