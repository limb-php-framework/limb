<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/fs/src/lmbFileLocator.class.php');
lmb_require('limb/fs/src/lmbCachingFileLocator.class.php');
lmb_require('limb/fs/src/lmbIncludePathFileLocations.class.php');

/**
 * class lmbFsTools.
 *
 * @package fs
 * @version $Id$
 */
class lmbFsTools extends lmbAbstractTools
{
  protected $file_locators = array();

  function findFileByAlias($alias, $paths, $locator_name = null)
  {
    $locator = $this->toolkit->getFileLocator($paths, $locator_name);
    return $locator->locate($alias);
  }

  function tryFindFileByAlias($alias, $paths, $locator_name = null)
  {
    try
    {
      $file = $this->findFileByAlias($alias, $paths, $locator_name);
    }
    catch(lmbFileNotFoundException $e)
    {
      return null;
    }
    return $file;
  }

  function getFileLocator($paths, $locator_name = null)
  {
    if(!$locator_name)
      $locator_name = md5($paths);

    if(isset($this->file_locators[$locator_name]))
       return $this->file_locators[$locator_name];

    if(is_array($paths))
      $file_locations = new lmbIncludePathFileLocations($paths);
    else
      $file_locations = new lmbIncludePathFileLocations(explode(';', $paths));

    if(defined('LIMB_VAR_DIR'))
      $locator = new lmbCachingFileLocator(new lmbFileLocator($file_locations),
                                           LIMB_VAR_DIR . '/locators/',
                                           $locator_name);
    else
      $locator = new lmbFileLocator($file_locations);

    $this->file_locators[$locator_name] = $locator;
    return $locator;
  }
}

