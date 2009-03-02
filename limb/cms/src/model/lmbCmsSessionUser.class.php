<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/cms/src/model/lmbCmsUser.class.php');

/**
 * class lmbCmsClassName.
 *
 * @package cms
 * @version $Id$
 */

class lmbCmsSessionUser extends lmbObject
{
  protected $user_id;
  protected $is_logged_in;
  protected $user;

  function getUser()
  {
    if(is_object($this->user))
      return $this->user;

    if($this->_isValidSession())
    {
      $this->user = lmbActiveRecord :: findById('lmbCmsUser', $this->user_id);
      $this->user->setLoggedIn($this->is_logged_in);
    }
    else
      $this->user = new lmbCmsUser();

    return $this->user;
  }

  function setUser($user)
  {
    $this->user = $user;
  }

  protected function _isValidSession()
  {
    return is_integer($this->user_id);
  }

  function __sleep()
  {
    $user = $this->getUser();
    $this->user_id = $user->getId();
    $this->is_logged_in = $user->isLoggedIn();

    return array('user_id', 'is_logged_in');
  }
}


