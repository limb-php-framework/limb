<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Abstract image convertor
 *
 * @package imagekit
 * @version $Id: lmbAbstractImageConvertor.class.php 6333 2007-09-24 16:38:22Z cmz $
 */
abstract class lmbAbstractImageConvertor
{
  protected $container = null;

  function __call($name, $args)
  {
    $params = (isset($args[0]) && is_array($args[0])) ? $args[0] : array();
    return $this->applyFilter($name, $params);
  }

  protected function applyFilter($name, $params)
  {
    $filter = $this->createFilter($name, $params);
    $filter->apply($this->container);
    return $this;
  }

  function load($file_name, $type = '')
  {
    $this->container = $this->createImageContainer($file_name, $type);
    return $this;
  }

  function apply($name)
  {
    $args = func_get_args();
    $params = (isset($args[1]) && is_array($args[1])) ? $args[1] : array();
    return $this->applyFilter($name, $params);
  }

  function applyBatch($batch)
  {
    foreach($batch as $filter)
    {
      list($name, $params) = each($filter);
      $this->applyFilter($name, $params);
    }
    return $this;
  }

  function save($file_name = null, $type = '')
  {
    if($type)
      $this->container->setOutputType($type);
    $this->container->save($file_name);
    $this->container = null;
    return $this;
  }

  protected function loadFilter($dir, $name, $prefix)
  {
    $class = 'lmb'.$prefix.ucfirst($name).'ImageFilter';
    $full_path = $dir.'/'.$class.'.class.php';
    lmb_require($full_path);
    return $class;
  }

  abstract protected function createFilter($name, $params);

  abstract protected function createImageContainer($file_name, $type = '');

  abstract function isSupportConversion($file, $src_type = '', $dest_type = '');
}
?>
