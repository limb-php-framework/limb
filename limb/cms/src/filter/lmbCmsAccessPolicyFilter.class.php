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
    $this->toolkit = lmbToolkit :: instance();
    $this->current_controller = $this->toolkit->getDispatchedController();

    $user = $this->toolkit->getCmsUser();

    $current_path = '/' . ltrim($this->toolkit->getRequest()->getUriPath(), '/');

    $controller_name = $this->current_controller->getName();

    if(strpos($controller_name, 'admin') === 0)
    {
      if(!$user->isLoggedIn())
      {
        $this->toolkit->flashMessage("Вы не авторизованы");
        $this->toolkit->redirect(array('controller' => 'user', 'action' => 'login'), null, '?redirect=' . $current_path);
        return;
      }
      elseif(!$this->_allowAccess($user))
      {
        $this->toolkit->flashMessage("Недостаточно прав доступа к разделу");

        if('display' != $this->current_controller->getCurrentAction())
          $this->current_controller->closePopup();
        else
          $this->toolkit->redirect(array('controller' => 'admin', 'action' => 'display'));

        return;
      }
    }

    $filter_chain->next();
  }

  protected function _allowAccess($user)
  {
    $acces_rules = $this->toolkit->getConf('roles')->get($user->getRoleType());
    if(!is_array($acces_rules))
      return false;

    $this->current_controller = $this->toolkit->getDispatchedController();
    $current_action = $this->current_controller->getCurrentAction();

    if(isset($acces_rules['restricted_controllers']) &&
       in_array($this->current_controller->getName(), $acces_rules['restricted_controllers']))
      return false;

    if(isset($acces_rules['restricted_actions'][$this->current_controller->getName()]) &&
       in_array($current_action, $acces_rules['restricted_actions'][$this->current_controller->getName()]))
      return false;

    return true;
  }
}


