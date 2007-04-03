<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWactTemplateLocator.class.php 5241 2007-03-14 13:03:47Z serega $
 * @package    view
 */
require_once('limb/wact/src/locator/WactTemplateLocator.interface.php');

class lmbWactTemplateLocator implements WactTemplateLocator
{
  protected $locator;

  function __construct($locator)
  {
    $this->locator = $locator;
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
    return LIMB_VAR_DIR . '/compiled/' . md5($full_path . serialize($params)) . '.php';
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
?>