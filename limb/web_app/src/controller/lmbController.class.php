<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbController.class.php 5528 2007-04-04 15:08:50Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/controller/lmbAbstractController.class.php');
lmb_require('limb/web_app/src/validation/lmbWebAppErrorList.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');

class lmbController extends lmbAbstractController
{
  protected $toolkit;
  protected $request;
  protected $response;
  protected $session;
  protected $view;
  protected $error_list;
  protected $validator;
  protected $form_id;

  function __construct()
  {
    parent :: __construct();

    $this->toolkit = lmbToolkit :: instance();
    $this->request = $this->toolkit->getRequest();
    $this->response = $this->toolkit->getResponse();
    $this->session = $this->toolkit->getSession();
    $this->view = $this->toolkit->getView();
    $this->error_list = new lmbWebAppErrorList();
    $this->validator = new lmbValidator($this->error_list);
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

    if($this->_findTemplateForAction($action))
      return true;

    return false;
  }

  function performAction()
  {
    if(method_exists($this, $this->_mapCurrentActionToMethod()))
    {
      if($template_path = $this->_findTemplateForAction($this->current_action))
        $this->setTemplate($template_path);

      $method = $this->_mapCurrentActionToMethod($this->_mapCurrentActionToMethod());
      $res = $this->$method();

      if(is_string($res))
        $this->response->write($res);
      elseif($this->response->isEmpty() && !$this->view->getTemplate())
        $this->response->write('Default empty output for controller "' . get_class($this) . '" action "' . $this->current_action . '"');

      return;
    }
    elseif($template_path = $this->_findTemplateForAction($this->current_action))
    {
      $this->setTemplate($template_path);
      return;
    }

    throw new lmbException('No method defined in controller "' .
                           $this->getName(). '" for action "' . $this->current_action . '" ' .
                           'and no appropriate template found');
  }

  function useForm($form_id)
  {
    $this->form_id = $form_id;
    $this->view->setFormErrors($form_id, $this->error_list);
  }

  function setTemplate($template_path)
  {
    $this->view->setTemplate($template_path);
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

  /**
   * @deprecated
   */
  function setViewFormDatasource($datasource, $form_id = null)
  {
    $this->setFormDatasource($datasource, $form_id);
  }

  function redirect($params_or_url = array(), $route_url = null)
  {
    $this->toolkit->redirect($params_or_url, $route_url);
  }

  function flashError($message)
  {
    $this->toolkit->flashError($message);
  }

  function flashMessage($message)
  {
    $this->toolkit->flashMessage($message);
  }

  function flash($message)
  {
    $this->flashMessage($message);
  }

  function closePopup()
  {
    $this->response->write('<html><script>window.opener.focus();window.opener.location.reload();window.close();</script></html>');
  }

  protected function _mapCurrentActionToMethod()
  {
    return $this->_mapActionToMethod($this->current_action);
  }

  protected function _mapActionToMethod($action)
  {
    return lmb_camel_case('do_' . $action);
  }
}

?>