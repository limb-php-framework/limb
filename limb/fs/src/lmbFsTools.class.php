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

class lmbFsTools extends lmbAbstractTools
{
  protected $file_locators = array();

  function findFileAlias($name, $paths, $files_group)
  {
    $locator = $this->toolkit->getFileLocator($paths, $files_group);
    return $locator->locate($name);
  }

  function getFileLocator($path, $files_group)
  {
    if(isset($this->file_locators[$path][$files_group]))
       return $this->file_locators[$path][$files_group];

    $file_locations = new lmbIncludePathFileLocations(explode(';', $path));

    if(defined('LIMB_VAR_DIR'))
      $locator = new lmbCachingFileLocator(new lmbFileLocator($file_locations),
                                           LIMB_VAR_DIR . '/locators/',
                                           $files_group);
    else
      $locator = new lmbFileLocator($file_locations);

    $this->file_locators[$path][$files_group] = $locator;
    return $locator;
  }
}
?>
