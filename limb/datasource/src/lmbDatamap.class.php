<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDatamap.class.php 5386 2007-03-28 12:56:31Z pachanga $
 * @package    datasource
 */
lmb_require('limb/classkit/src/lmbProxyResolver.class.php');
lmb_require('limb/core/src/exception/lmbException.class.php');

class lmbDatamap
{
  protected $map = array();

  function addMapping($source, $dest = null)
  {
    if(is_null($dest))
      $dest = $source;

    $this->map[$source] = $dest;
  }

  function bindDestinationToSource($dest, $new_source)
  {
    if($old_source = array_search($dest, $this->map))
    {
      $this->map[$new_source] = $dest;
      unset($this->map[$old_source]);
    }
  }

  function export()
  {
    return $this->map;
  }

  function map($source, &$dest)
  {
    $this->_doMap($this->map, $source, $dest);
  }

  function reverseMap($source, &$dest)
  {
    $this->_doMap(array_flip($this->map), $source, $dest);
  }

  protected function _doMap($map_array, &$source, &$dest)
  {
    if(is_object($source))
      $source = lmbProxyResolver :: resolve($source);

    if(is_object($dest))
      $dest = lmbProxyResolver :: resolve($dest);

    foreach($map_array as $from => $to)
    {
      if(!is_null($value = $this->_getValue($source, $from)))
        $this->_setValue($dest, $to, $value);
    }
  }

  protected function _getValue(&$source, $name)
  {
    if(is_object($source))
    {
      $method_name = 'get' . lmb_camel_case($name, true);

      if(method_exists($source, $method_name))
        return $source->$method_name();
      elseif(method_exists($source, 'get'))
        return $source->get($name);
      else
        throw new lmbException("Could not get '$name' field from " . get_class($source));
    }
    elseif(isset($source[$name]))
      return $source[$name];
  }

  protected function _setValue(&$dest, $name, $value)
  {
    if(is_object($dest))
    {
      $method_name = 'set' . lmb_camel_case($name, true);

      if(method_exists($dest, $method_name))
        return $dest->$method_name($value);
      elseif(method_exists($dest, 'set'))
        return $dest->set($name, $value);
      else
        throw new lmbException("Could not set '$name' field for " . get_class($dest));
    }
    else
      $dest[$name] = $value;
  }
}

?>
