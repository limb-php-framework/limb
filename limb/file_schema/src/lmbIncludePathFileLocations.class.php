<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIncludePathFileLocations.class.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/file_schema/src/lmbFileLocations.interface.php');

class lmbIncludePathFileLocations implements lmbFileLocations
{
  protected $paths;

  function __construct($paths = array())
  {
    $this->paths = $paths;
  }

  function getLocations($params = array())
  {
    $resolved = array();
    foreach($this->paths as $path)
    {
      foreach(lmb_glob($path) as $dir)
        $resolved[] = $dir;
    }
    return $resolved;
  }
}

?>