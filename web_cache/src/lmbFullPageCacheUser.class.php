<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbFullPageCacheUser.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheUser.class.php 7686 2009-03-04 19:57:12Z korchasa $
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


