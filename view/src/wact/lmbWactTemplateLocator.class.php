<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/wact/src/locator/WactTemplateLocator.interface.php');

/**
 * class lmbWactTemplateLocator.
 *
 * @package view
 * @version $Id$
 */
class lmbWactTemplateLocator implements WactTemplateLocator
{
  protected $locator;
  protected $cache_dir;

  function __construct($locator, $cache_dir)
  {
    $this->locator = $locator;
    $this->cache_dir = $cache_dir;
  }

  function setFileLocator($file_locator)
  {
    $this->locator = $file_locator;
  }

  function getFileLocator()
  {
    return $this->locator;
  }

  function locateSourceTemplate($file)
  {
    $params = $this->_collectParams();
    try
    {
      return $this->locator->locate($file, $params);
    }
    catch(lmbFileNotFoundException $e){}
  }

  function locateCompiledTemplate($file)
  {
    $params = $this->_collectParams();
    $full_path = $this->locateSourceTemplate($file);
    return $this->cache_dir . '/' . md5($full_path . serialize($params)) . '.php';
  }

  protected function _collectParams()
  {
    return array();
  }

  function readTemplateFile($fileName)
  {
    if(file_exists($fileName))
      return file_get_contents($fileName);
  }
}

