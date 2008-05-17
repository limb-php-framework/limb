<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

lmb_require('limb/acl/src/lmbRoleProviderInterface.interface.php');
lmb_require('limb/acl/src/lmbResourceProviderInterface.interface.php');
lmb_require('limb/acl/src/lmbRolesResolverInterface.interface.php');
lmb_require('limb/acl/src/lmbAclException.class.php');

class lmbAcl
{
  protected $_default_policy;
  
  protected $_roles = array();
  protected $_resources = array();
  public $_roles_rules = array();
  public $_resources_rules = array();
  public $_privileges_rules = array();  

  function __construct($default_policy = false)
  {
    $this->_default_policy = $default_policy;
  }

  function addRole($role, $parents = array())
  {
    if(!is_array($parents))
      $parents = array($parents);

    foreach($parents as $parent)
    {
      if(!$this->isRoleExist($parent))
      throw new lmbAclException('Parent role not exist', array(
        'role' => $role,
        'parent' => $parent,
      ));
    }

    $this->_roles[$role] = $parents;

    return $this;
  }

  function getRoles()
  {
    return array_keys($this->_roles);
  }

  function isRoleExist($role)
  {
    return array_key_exists($role, $this->_roles);
  }

  function getRoleInherits($role)
  {
    $inherits = $this->_roles[$role];

    if(!count($inherits))
      return array();

    foreach($inherits as $inherit)
      $inherits  = array_merge($inherits, $this->getRoleInherits($inherit));

    return $inherits;
  }

  function addResource($resource, $parents = array())
  {
    if(!is_array($parents))
      $parents = array($parents);

    foreach($parents as $parent)
    {
      if(!$this->isResourceExist($parent))
      throw new lmbAclException('Parent role not exist', array(
        'role' => $resource,
        'parent' => $parent,
      ));
    }

    $this->_resources[$resource] = $parents;

    return true;
  }

  function getResources()
  {
    return array_keys($this->_resources);
  }

  function isResourceExist($resource)
  {
    return array_key_exists($resource, $this->_resources);
  }

  function getResourceInherits($resource)
  {
    $inherits = $this->_resources[$resource];

    if(!count($inherits))
      return array();

    foreach($inherits as $inherit)
      $inherits  = array_merge($inherits, $this->getResourceInherits($inherit));

    return $inherits;
  }

  protected function _isExistRoleRule($role, $privilege)
  {
    if(!array_key_exists($role, $this->_roles_rules))
      return false;
    if(is_array($this->_roles_rules[$role]))
      return array_key_exists($privilege, $this->_roles_rules[$role]);
    return true;
  }

  protected function _applyRoleRule($role, $rule, $privileges)
  {
    $this->_removeResourceRule($role);
    if(0 === count($privileges))
      $this->_roles_rules[$role] = $rule;
    else
      foreach($privileges as $privilege)
          $this->_roles_rules[$role][$privilege] = $rule;
  }

  protected function _getRoleRule($role, $privelege)
  {
    if(!is_array($this->_roles_rules[$role]))
      return $this->_roles_rules[$role];
    else
    {
      if(!array_key_exists($privelege, $this->_roles_rules[$role]))
        return false;
      return $this->_roles_rules[$role][$privelege];
    }
  }

  protected function _removeResourceRule($role)
  {
    unset($this->_resources_rules[$role]);
  }

  protected function _isExistResourceRule($role, $resource)
  {
    if(!array_key_exists($role, $this->_resources_rules))
      return false;
    return array_key_exists($resource, $this->_resources_rules[$role]);
  }

  protected function _applyResourceRule($role, $resource, $rule)
  {
    if(!array_key_exists($role, $this->_resources_rules))
      $this->_resources_rules[$role] = array();

    $this->_removePrivilegesRule($role, $resource);

    $this->_resources_rules[$role][$resource] = $rule;
  }

  protected function _getResourceRule($role, $resource)
  {
    return $this->_resources_rules[$role][$resource];
  }

  protected function _isExistPrivilegeRule($role, $resource, $privilege)
  {
    if(!array_key_exists($role, $this->_privileges_rules))
      return false;
    if(!array_key_exists($resource, $this->_privileges_rules[$role]))
      return false;
    return array_key_exists($privilege, $this->_privileges_rules[$role][$resource]);
  }

  protected function _applyPrivilegesRule($role, $resource, $privileges, $rule)
  {
    if(!array_key_exists($role, $this->_privileges_rules))
      $this->_privileges_rules[$role] = array();

    if(!array_key_exists($resource, $this->_privileges_rules[$role]))
      $this->_privileges_rules[$role][$resource] = array();

    foreach($privileges as $privilege)
      $this->_privileges_rules[$role][$resource] = array_merge($this->_privileges_rules[$role][$resource], array($privilege => $rule));
  }

  protected function _removePrivilegesRule($role, $resource)
  {
    unset($this->_privileges_rules[$role][$resource]);
  }

  protected function _getPrivilegeRule($role, $resource, $privilege)
  {
    return $this->_privileges_rules[$role][$resource][$privilege];
  }

  protected function _checkResource($resource)
  {
    if(!is_null($resource) && !$this->isResourceExist($resource))
      throw new lmbAclException('Resource not exist', array('resource' => $resource));
  }

  function isAllowed($role, $resource = null, $privilege = null)
  {
    if($resource instanceof lmbRolesResolverInterface)
      if($resolved_role = $resource->getRoleFor($role))
        $role = $resolved_role;

    if($resource instanceof lmbResourceProviderInterface )
      $resource = $resource->getResource();

    $this->_checkResource($resource);

    if($role instanceof lmbRoleProviderInterface)
      $role = $role->getRole();

    if(!$this->isRoleExist($role))
      throw new lmbAclException('Role not exist', array('role' => $role));   

    if($this->_isExistPrivilegeRule($role, $resource, $privilege))
      return $this->_getPrivilegeRule($role, $resource, $privilege);

    if($this->_isExistResourceRule($role, $resource))
      return $this->_getResourceRule($role, $resource);

    if($this->_isExistRoleRule($role, $privilege))
      return $this->_getRoleRule($role, $privilege);

    foreach($this->getRoleInherits($role) as $inherit)
      if($this->isAllowed($inherit, $resource, $privilege))
        return true;
      
    return $this->_default_policy;
  }

  function setRule($role, $resource = null, $privileges = array(), $rule)
  {
    if(!is_array($privileges))
      $privileges = array($privileges);

    if(!$this->isRoleExist($role))
      throw new lmbAclException('Role not exist', array('role' => $role));

    if(is_null($resource))
      return $this->_applyRoleRule($role, $rule, $privileges);
    else
    {
      $this->_checkResource($resource);

      if(0 === count($privileges))
        $this->_applyResourceRule($role, $resource, $rule);
      else
        $this->_applyPrivilegesRule($role, $resource, $privileges, $rule);
    }
  }

  function allow($role, $resource = null, $privileges = array())
  {
    $this->setRule($role, $resource, $privileges, true);
  }

  function deny($role, $resource = null, $privileges = array())
  {
    $this->setRule($role, $resource, $privileges, false);
  }

}
