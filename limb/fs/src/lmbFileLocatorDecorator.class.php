<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/fs/src/lmbFileLocator.class.php');

/**
 * class lmbFileLocatorDecorator.
 *
 * @package fs
 * @version $Id$
 */
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


