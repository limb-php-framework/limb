<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/fs/src/lmbFileLocations.interface.php');

/**
 * class lmbIncludePathFileLocations.
 *
 * @package fs
 * @version $Id$
 */
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


