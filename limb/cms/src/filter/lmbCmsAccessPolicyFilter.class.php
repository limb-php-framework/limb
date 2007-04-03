<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

class lmbCmsAccessPolicyFilter implements lmbInterceptingFilter
{
  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();
    $user = $toolkit->getUser();
    $current_path = '/' . ltrim($toolkit->getRequest()->getUriPath(), '/');

    if(strpos($current_path, '/admin') === 0 && !$user->isLoggedIn())
    {
      $toolkit->flashMessage("Not enough access permissions");
      $toolkit->redirect(array('controller' => 'user', 'action' => 'login'), '?redirect=' . $current_path);
      return;
    }

    $filter_chain->next();
  }
}

?>