<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/model/lmbCmsUserRoles.class.php');

/**
 * class lmbCmsUser.
 *
 * @package cms
 * @version $Id$
 */

class lmbCmsUser extends lmbActiveRecord
{
  protected $_db_table_name = 'user';

  protected $password;
  protected $is_logged_in = false;

  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('name');
    $validator->addRequiredRule('login');
    $validator->addRequiredRule('email');

    lmb_require('limb/validation/src/rule/lmbMatchRule.class.php');
    $validator->addRule(new lmbMatchRule('password', 'password_repeat'));

    lmb_require('limb/cms/src/validation/rule/lmbCmsUserUniqueFieldRule.class.php');
    $validator->addRule(new lmbCmsUserUniqueFieldRule('login', $this));
    $validator->addRule(new lmbCmsUserUniqueFieldRule('email', $this));

    lmb_require('limb/validation/src/rule/lmbEmailRule.class.php');
    $validator->addRule(new lmbEmailRule('email'));
    return $validator;
  }

  protected function _createInsertValidator()
  {
    $validator = $this->_createValidator();
    $validator->addRequiredRule('password');
    return $validator;
  }

  protected function _onBeforeSave()
  {
    if($this->password)
      $this->setHashedPassword(self :: cryptPassword($this->password));
  }

  function login($login, $password)
  {
    $hashed_password = lmbCmsUser :: cryptPassword($password);

    $criteria = new lmbSQLFieldCriteria('login', $login);
    $criteria->addAnd(new lmbSQLFieldCriteria('hashed_password', $hashed_password));

    if($user = lmbActiveRecord :: findFirst('lmbCmsUser', array('criteria' => $criteria)))
    {
      $this->import($user);
      $this->setIsNew(false);
      $this->setIsLoggedIn(true);
      return true;
    }
    else
    {
      $this->setIsLoggedIn(false);
      return false;
    }
  }

  function logout()
  {
    $this->reset();
    $this->is_logged_in = false;
  }

  function isLoggedIn()
  {
    return $this->is_logged_in;
  }

  function setLoggedIn($logged_in)
  {
    $this->is_logged_in = $logged_in;
  }

  function cryptPassword($password)
  {
    return md5($password);
  }

  function passwordIsCorrect($password)
  {
    return ($this->getHashedPassword() == self :: cryptPassword($password));
  }

  function generatePassword()
  {
    $alphabet = array(
        array('b','c','d','f','g','h','g','k','l','m','n','p','q','r','s','t','v','w','x','z',
              'B','C','D','F','G','H','G','K','L','M','N','P','Q','R','S','T','V','W','X','Z'),
        array('a','e','i','o','u','y','A','E','I','O','U','Y'),
    );

    $new_password = '';
    for($i = 0; $i < 9 ;$i++)
    {
      $j = $i%2;
      $min_value = 0;
      $max_value = count($alphabet[$j]) - 1;
      $key = rand($min_value, $max_value);
      $new_password .= $alphabet[$j][$key];
    }
    return $new_password;
  }

  function getCurrentRole()
  {
    if($role_id = $this->getRoleId())
      return lmbCmsUserRoles :: createRole($role_id);
    else
    {
      $this->setRoleId(lmbCmsUserRoles :: ADMIN);
      return lmbCmsUserRoles :: createRole(lmbCmsUserRoles :: ADMIN);
    }
  }

  function getIsAdmin()
  {
    return $this->getRoleId() == lmbCmsUserRoles :: ADMIN;
  }

}

