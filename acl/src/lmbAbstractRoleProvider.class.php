<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

abstract class lmbAbstractRoleProvider
{
  protected $_role = null;

  function getRole()
  {
    if(is_null($this->_role))
      throw new lmbAclException('Role provider must have filled _role property');
    return $this->_role;
  }
}
