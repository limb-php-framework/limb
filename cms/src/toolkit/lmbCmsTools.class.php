<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/tree/src/lmbMPTree.class.php');
lmb_require('limb/cms/src/model/lmbCmsSessionUser.class.php');

/**
 * class lmbCmsTools.
 *
 * @package cms
 * @version $Id: lmbCmsTools.class.php 7619 2009-02-10 15:07:35Z step $
 */
class lmbCmsTools extends lmbAbstractTools
{
  protected $tree;
  protected $user;

  function getCmsTree($tree_name = 'node')
  {
    if(isset($this->tree[$tree_name]) && is_object($this->tree[$tree_name]))
      return $this->tree[$tree_name];

    $this->tree[$tree_name] = new lmbMPTree($tree_name);

    return $this->tree[$tree_name];
  }

  function setCmsTree($tree)
  {
    $this->tree = $tree;
  }

  function getCmsUser()
  {
    if(is_object($this->user))
      return $this->user;

    $session = lmbToolkit :: instance()->getSession();
    if(!is_object($session_user = $session->get('lmbCmsSessionUser')))
    {
      $session_user = new lmbCmsSessionUser();
      $session->set('lmbCmsSessionUser', $session_user);
    }

    $this->user = $session_user->getUser();

    return $this->user;
  }

  function resetCmsUser()
  {
    $this->setCmsUser(null);
    $session = lmbToolkit :: instance()->getSession();
    $session->destroy('lmbCmsSessionUser');
  }

  function setCmsUser($user)
  {
    $this->user = $user;
  }
}


