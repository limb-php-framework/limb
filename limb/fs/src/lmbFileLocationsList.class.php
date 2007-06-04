<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */
lmb_require('limb/fs/src/lmbFileLocations.interface.php');

class lmbFileLocationsList implements lmbFileLocations
{
  protected $locations = array();

  function __construct()
  {
    if(($args = func_get_args()) > 0)
      $this->locations = $args;
  }

  function getLocations($params = array())
  {
    return $this->_collectLocations($this->locations, $params);
  }

  function _collectLocations($locations, $params)
  {
    $result = array();
    foreach($locations as $location)
    {
      if(is_object($location) && $location instanceof lmbFileLocations)
      {
        foreach($location->getLocations($params) as $sub_location)
          $result[] = $sub_location;
      }
      elseif(!is_array($location))
        $result[] = $location;
      else
        $result = array_merge($result, $this->_collectLocations($location, $params));
    }
    return $result;
  }
}

?>
