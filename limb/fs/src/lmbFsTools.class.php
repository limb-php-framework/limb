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

  function findFileByAlias($alias, $paths, $locator_name)
  {
    $locator = $this->toolkit->getFileLocator($paths, $locator_name);
    return $locator->locate($alias);
  }

  function getFileLocator($paths, $locator_name)
  {
    if(isset($this->file_locators[$locator_name]))
       return $this->file_locators[$locator_name];

    $file_locations = new lmbIncludePathFileLocations(explode(';', $paths));

    if(defined('LIMB_VAR_DIR'))
      $locator = new lmbCachingFileLocator(new lmbFileLocator($file_locations),
                                           LIMB_VAR_DIR . '/locators/',
                                           $locator_name);
    else
      $locator = new lmbFileLocator($locator_name);

    $this->file_locators[$locator_name] = $locator;
    return $locator;
  }
}
?>
