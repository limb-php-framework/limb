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
 * @version $Id$
 */
abstract class lmbAbstractImageConvertor
{
  protected $filters = array();
  protected $container = null;

  function __construct()
  {

  }

  function addFilter($name, $params)
  {
    $this->filters[] = $this->createFilter($name, $params);
  }

  function run($src, $dest, $src_type = '', $dest_type = '')
  {
    $container = $this->createImageContainer($src, $src_type);
    foreach($this->filters as $filter) $filter->run($container);
    $container->save($dest, $dest_type);
  }

  function load($file_name, $type = '')
  {
    $this->container = $this->createImageContainer($file_name, $type);
    return $this;
  }

  function apply($name)
  {
    $args = func_get_args();
    $params = array_slice($args, 1);
    $filter = $this->createFilter($name, $params);
    $filter->run($this->container);
    return $this;
  }

  function save($file_name = null, $type = '')
  {
    $this->container->save($file_name, $type);
    $this->container = null;
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