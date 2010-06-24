<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cms/src/model/lmbCmsUserRole.class.php');
/**
 * class lmbCmsUserRoles.
 *
 * @package cms
 * @version $Id$
 */

class lmbCmsUserRoles
{
  const EDITOR         = 'editor';
  const ADMIN          = 'admin';

  function createRole($role_id)
  {
    $roles = self :: getRoles();
    if(isset($roles[$role_id]))
      return $roles[$role_id];
  }

  function createRolesByIds($roles_ids)
  {
    $roles = array();
    foreach($roles_ids as $role_id)
      $roles[$role_id] = self :: createRole($role_id);
    return $roles;
  }

  function createAdminRole()
  {
    return new lmbCmsUserRole(self :: ADMIN, 'Администратор', 'Админ');
  }

  function createEditorRole()
  {
    return new lmbCmsUserRole(self :: EDITOR, 'Редактор', 'Ред');
  }

  function getRoles()
  {
    return array(self :: ADMIN => self :: createAdminRole(),
                 self :: EDITOR => self :: createEditorRole());
  }

  function fetch()
  {
    return new lmbCollection(self :: getRoles());
  }

  function hasRoles($role_ids)
  {
    $existing_roles = self :: getRoles();

    foreach($role_ids as $id)
    {
      if(!isset($existing_roles[$id]))
        return false;
    }
    return true;
  }

  function getRoleName($role_id)
  {
    $existing_roles = self :: getRoles();
    if(isset($existing_roles[$role_id]))
      return $existing_roles[$role_id]->getName();
    else
      return '';
  }
}


