<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    core
 */

class lmbMixable
{
  protected $mixins = array();
  protected $mixins_loaded = false;
  protected $mixins_signatures = array();

  function mixin($mixin)
  {
    $this->mixins[] = $mixin;
  }

  function __call($method, $args)
  {
    $this->_ensureSignatures();

    if(!isset($this->mixins_signatures[$method]))
      throw new lmbException('Mixable does not support method "' . $method . '" (no such signature)',
                              array('method' => $method));


    return call_user_func_array(array($this->mixins_signatures[$method], $method), $args);
  }

  function get($name)
  {
    if(isset($this->$name))
      return $this->$name;
  }

  protected function _ensureSignatures()
  {
    if($this->mixins_loaded)
      return;

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
      $obj->setOwner($this);

      foreach(get_class_methods($class) as $method)
        $this->mixins_signatures[$method] = $obj;
    }

    $this->mixins_loaded = true;
  }
}
?>
