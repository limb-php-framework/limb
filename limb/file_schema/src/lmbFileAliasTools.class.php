<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileAliasTools.class.php 5548 2007-04-06 07:39:14Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/file_schema/src/lmbFileLocator.class.php');
lmb_require('limb/file_schema/src/lmbCachingFileLocator.class.php');
lmb_require('limb/file_schema/src/lmbIncludePathFileLocations.class.php');

class lmbFileAliasTools extends lmbAbstractTools
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
