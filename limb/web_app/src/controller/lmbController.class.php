<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_env_setor('LIMB_CONTROLLER_CACHE_ENABLED', true);

lmb_require('limb/core/src/lmbClassPath.class.php');
lmb_require('limb/core/src/lmbMixable.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/view/src/lmbDummyView.class.php');

/**
 * class lmbController.
 *
 * @package web_app
 * @version $Id: lmbController.class.php 8117 2010-01-31 11:20:22Z Forumsky $
 */
class lmbController
{
  /**
   * @var array array of mixins
   */
  protected $mixins = array();

  /**
   * @var object lmbMixable instance
   */
  protected $mixed;

  /**
   * @var string name of the controller
   */
  protected $name;

  /**
   * @var string default action that will be performed by performAction() if no current_action was speficified
   */
  protected $default_action = 'display';

  /**
   * @var string
   */
  protected $current_action;

  /**
   * @var array a action to template cached map
   */
  protected $action_template_map = array();

  /**
   * @var boolean
   */
  protected $map_changed = false;

  /**
   * @var object lmbToolkit instance
   */
  protected $toolkit;
  /**
   *
   * @var lmbHttpRequest
   */
  protected $request;

  /**
   *
   * @var lmbHttpResponse
   */
  protected $response;
  /**
   *
   * @var lmbSession
   */
  protected $session;
  /**
   *
   * @var lmbView
   */
  protected $view;
  /**
   *
   * @var array
   */
  protected $error_list;
  /**
   *
   * @var lmbValidator
   */
  protected $validator;

  protected $form_id;
  protected $in_popup = true;
  protected $is_forwarded = false;

  function __construct()
  {
    $this->mixed = new lmbMixable();
    $this->mixed->setOwner($this);
    foreach($this->mixins as $mixin)
      $this->mixed->mixin($mixin);

    if(!$this->name)
     $this->name = $this->_guessName();

    $this->toolkit = lmbToolkit :: instance();

    $this->request = $this->toolkit->getRequest();
    $this->response = $this->toolkit->getResponse();
    $this->session = $this->toolkit->getSession();
    $this->view = $this->toolkit->getView();//this is a dummy view, which will be replaced with a concrete one with setTemplate() method
    $this->error_list = new lmbErrorList();
    $this->validator = new lmbValidator($this->error_list);

    $this->_loadCache();
  }

  function getDefaultAction()
  {
    return $this->default_action;
  }

  function setCurrentAction($action)
  {
    $this->current_action = $action;
  }

  function getCurrentAction()
  {
    return $this->current_action;
  }

  protected function _guessName()
  {
    if($pos = strpos(get_class($this), 'Controller'))
      return lmb_under_scores(substr(get_class($this), 0, $pos));
  }

  /**
   *  Returns {@link $name}
   *  @return string
   */
  function getName()
  {
    return $this->name;
  }

  function getView()
  {
    return $this->view;
  }

  function validate($dataspace)
  {
    $this->validator->validate($dataspace);
    return $this->validator->isValid();
  }

  function actionExists($action)
  {
    if(method_exists($this, $this->_mapActionToMethod($action)))
      return true;

    if($this->findTemplateForAction($action))
      return true;

    return false;
  }

  function performAction()
  {
    if($this->is_forwarded)
    {
      return false;
    }

    if(method_exists($this, $method = $this->_mapCurrentActionToMethod()))
    {
      if($template_path = $this->findTemplateForAction($this->current_action))
      {
        $this->setTemplate($template_path);
      }

      $res = $this->$method();

      $this->_passLocalAttributesToView();

      if(is_string($res))
        $this->response->write($res);

      return $res;
    }
    elseif($template_path = $this->findTemplateForAction($this->current_action))
    {
      $this->setTemplate($template_path);
      $this->_passLocalAttributesToView();
      return;
    }

    throw new lmbException('No method defined in controller "' .
                           $this->getName(). '" for action "' . $this->current_action . '" ' .
                           'and no appropriate template found');
  }

  function useForm($form_id, $datasource = null)
  {
    $this->form_id = $form_id;
    $this->view->setFormErrors($form_id, $this->error_list);

    if ($datasource)
      $this->setFormDatasource($datasource);
  }

  function setTemplate($template_path)
  {
    $view = $this->toolkit->createViewByTemplate($template_path);
    //copying stuff from dummy view, do we need this?
    $view->copy($this->view);
    $this->view = $view;
    $this->toolkit->setView($view);
  }

  protected function _passLocalAttributesToView()
  {
    foreach(get_object_vars($this) as $name => $value)
    {
      if($name{0} == '_')
        continue;
      $this->view->set($name, $value);
    }
  }

  function passToView($var, $value)
  {
    $this->view->set($var, $value);
  }

  function resetView()
  {
    $this->view->reset();
  }

  function setFormDatasource($datasource, $form_id = null)
  {
    if(!$form_id && !$this->form_id)
      throw new lmbException('There is no form id specified');

    if(!$form_id)
      $form_id = $this->form_id;

    $this->view->setFormDatasource($form_id, $datasource);
  }

  function redirect($params_or_url = array(), $route_url = null)
  {
    $this->toolkit->redirect($params_or_url, $route_url);
  }

  function flashError($message)
  {
    $this->toolkit->flashError($message);
  }

  function flashErrorAndRedirect($message, $redirect = array())
  {
    $this->flashError($message);
    $this->redirect($redirect);
  }

  function flashMessage($message)
  {
    $this->toolkit->flashMessage($message);
  }

  function flashAndRedirect($message, $redirect = array())
  {
    $this->flashMessage($message);
    $this->redirect($redirect);
  }

  function flash($message)
  {
    $this->flashMessage($message);
  }

  function addError($message, $fields = array(), $values = array())
  {
    $this->error_list->addError($message, $fields, $values);
  }

  function closePopup()
  {
    if(!$this->in_popup)
      return;

    $this->response->write('<html><script>if(window.opener){window.opener.focus();window.opener.location.reload();window.close();}</script></html>');
  }

  protected function _mapCurrentActionToMethod()
  {
    return $this->_mapActionToMethod($this->current_action);
  }

  protected function _mapActionToMethod($action)
  {
    return lmb_camel_case('do_' . $action);
  }

  function forward($controller_name, $action)
  {
    $this->is_forwarded = true;
    $controller = $this->toolkit->createController($controller_name);
    $controller->setCurrentAction($action);
    return $controller->performAction();
  }

  function forwardTo404()
  {
    return $this->forward('not_found', 'display');
  }

  function forwardTo500()
  {
    return $this->forward('server_error', 'display');
  }


  function __destruct()
  {
    $this->_saveCache();
  }

  function isCacheEnabled()
  {
    return (bool) lmb_env_get('LIMB_CONTROLLER_CACHE_ENABLED');
  }

  function _loadCache()
  {
    if($this->isCacheEnabled() && file_exists($cache = lmb_env_get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache'))
      $this->action_template_map = unserialize(file_get_contents($cache));
  }

  function _saveCache()
  {
    if($this->map_changed && $this->isCacheEnabled())
    {
      lmbFs :: safeWrite(lmb_env_get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache',
                         serialize($this->action_template_map));
    }
  }

  /**
   * Using this hacky method mixins can access controller variables
   * @param string variable name
   * @return mixed
   */
  function _get($name)
  {
    if(isset($this->$name))
      return $this->$name;
  }

  function findTemplateForAction($action)
  {
    if(isset($this->action_template_map[$this->name]) && isset($this->action_template_map[$this->name][$action]))
      return $this->action_template_map[$this->name][$action];

    $template_format = $this->getName() . '/' . $action;

    $template_path = $this->findTemplateByAlias($template_format);
    $this->action_template_map[$this->name][$action] = $template_path;
    $this->map_changed = true;

    return $template_path;
  }

  function findTemplateByAlias($template_format)
  {
    foreach($this->toolkit->getSupportedViewExtensions() as $ext)
    {
      if($template_path = $this->toolkit->locateTemplateByAlias($template_format . $ext))
      {
        return $template_path;
      }
    }
    return false;
  }
}


