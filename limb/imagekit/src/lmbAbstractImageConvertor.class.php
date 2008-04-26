<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/fs/src/lmbFileLocationsList.class.php');
lmb_require('limb/fs/src/lmbFileLocator.class.php');

/**
 * Abstract image convertor
 *
 * @package imagekit
 * @version $Id: lmbAbstractImageConvertor.class.php 6960 2008-04-26 20:45:33Z cmz $
 */
abstract class lmbAbstractImageConvertor
{
  protected $container = null;
  protected $params;
  
  function __construct($params = array())
  {    
    $this->params = $params;
  }
  
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
  
  /**
   * Return filter locator
   *
   * @return lmbFileLocator
   */
  protected function getFilterLocator()
  {
    $dirs = array();
    if(is_array($this->params['filters_scan_dirs']))
      $dirs = $this->params['filters_scan_dirs'];
    else
      $dirs['filters_scan_dirs'] = $this->params['filters_scan_dirs'];
      
    if(isset($this->params['add_filters_scan_dirs']))
    {
      if(is_array($this->params['add_filters_scan_dirs']))
        $dirs = array_merge($dirs, $this->params['add_filters_scan_dirs']);
      else
        $dirs[] = $this->params['add_filters_scan_dirs'];
    }
    
    return new lmbFileLocator(new lmbFileLocationsList($dirs));
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

  protected function loadFilter($name, $prefix)
  {
    $class = 'lmb'.$prefix.ucfirst($name).'ImageFilter';
    $full_path = $this->getFilterLocator()->locate($class.'.class.php');
    lmb_require($full_path);
    return $class;
  }

  abstract protected function createFilter($name, $params);

  abstract protected function createImageContainer($file_name, $type = '');

  abstract function isSupportConversion($file, $src_type = '', $dest_type = '');
}
