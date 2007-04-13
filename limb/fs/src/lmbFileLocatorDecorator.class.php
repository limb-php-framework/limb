<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileLocatorDecorator.class.php 5416 2007-03-29 10:46:58Z pachanga $
 * @package    fs
 */
lmb_require('limb/fs/src/lmbFileLocator.class.php');

class lmbFileLocatorDecorator extends lmbFileLocator
{
  protected $locator = null;

  function __construct($locator)
  {
    $this->locator = $locator;
  }

  function locate($alias, $params = array())
  {
    return $this->locator->locate($alias, $params);
  }

  function locateAll($alias = '')
  {
    return $this->locator->locateAll($alias);
  }

  function getFileLocations()
  {
    return $this->locator->getFileLocations();
  }
}

?>