<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

/**
 * class lmbCmsAccessPolicyFilter.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsAccessPolicyFilter implements lmbInterceptingFilter
{
  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();
    $user = $toolkit->getCmsUser();
    $current_path = '/' . ltrim($toolkit->getRequest()->getUriPath(), '/');

    if(strpos($current_path, '/admin') === 0 && !$user->isLoggedIn())
    {
      $toolkit->flashMessage("Not enough access permissions");
      $toolkit->redirect(array('controller' => 'user', 'action' => 'login'), null, '?redirect=' . $current_path);
      return;
    }

    $filter_chain->next();
  }
}


