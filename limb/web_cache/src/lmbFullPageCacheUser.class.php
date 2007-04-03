<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheUser.class.php 5013 2007-02-08 15:38:13Z pachanga $
 * @package    web_cache
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
