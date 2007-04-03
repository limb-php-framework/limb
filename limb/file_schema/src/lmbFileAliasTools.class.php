<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileAliasTools.class.php 5390 2007-03-28 13:14:41Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/file_schema/src/lmbFileLocator.class.php');
lmb_require('limb/file_schema/src/lmbCachingFileLocator.class.php');
lmb_require('limb/file_schema/src/lmbIncludePathFileLocations.class.php');

class lmbFileAliasTools extends lmbAbstractTools
{
  protected $file_locators = array();

  function findFileAlias($name, $paths, $file_group)
  {
    $locator = $this->toolkit->getFileLocator($paths, $file_group);
    return $locator->locate($name);
  }

  function getFileLocator($path, $file_group)
  {
    if(isset($this->file_locators[$path][$file_group]))
       return $this->file_locators[$path][$file_group];

    $file_locations = new lmbIncludePathFileLocations(explode(';', $path));

    if(defined('LIMB_VAR_DIR'))
      $locator = new lmbCachingFileLocator(new lmbFileLocator($file_locations), $file_group);
    else
      $locator = new lmbFileLocator($file_locations);

    $this->file_locators[$path][$file_group] = $locator;
    return $locator;
  }
}
?>
