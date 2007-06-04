<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheUser.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class lmbFullPageCacheUser
{
  protected $groups;

  function __construct($groups = array())
  {
    $this->groups = $groups;
  }

  function getGroups()
  {
    return $this->groups;
  }
}

?>
