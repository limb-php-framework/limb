<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cms
 * @version $Id$
 */

lmb_require('limb/web_app/src/controller/lmbController.class.php');
@define('LIMB_MODELS_INCLUDE_PATH', 'src/model;limb/*/src/model');

class CRUDController extends lmbController
{
  protected $class_name;
  protected $model_name;

  function performAction()
  {
    $this->model_name = $this->request->get('controller');
    $this->class_name = lmb_camel_case($this->model_name );

    try
    {
      $file_model = $this->toolkit->findFileByAlias($this->class_name . '.class.php', LIMB_MODELS_INCLUDE_PATH, 'model');
      lmb_require($file_model);

      parent :: performAction();
    } catch(lmbException $e)
    {
      $this->forward('not_found', 'display');
    }
  }

  function doDisplay ()
  {
    $this->passToView($this->model_name . '_list', lmbActiveRecord :: find($this->class_name , ''));
    $this->passToView('CRUD_list', lmbActiveRecord :: find($this->class_name , ''));
  }

  function doCreate()
  {
    $CRUD_object= new $this->class_name();

    $this->_performCreateOrEdit($CRUD_object);
  }

  function doEdit()
  {
    $id = (int)$this->request->getInteger('id');

    $CRUD_object = new $this->class_name($id);

    $this->_performCreateOrEdit($CRUD_object);
  }

  function doDelete()
  {
    $id = (int)$this->request->getInteger('id');

    $CRUD_object = new $this->class_name($id);
    $CRUD_object->destroy();

    $this->redirect();
  }

  function getName()
  {
    return $this->model_name;
  }

  protected function _performCreateOrEdit($CRUD_object)
  {
    if( $this->view->findChild('CRUD_form') )
      $this->useForm('CRUD_form');
    else
      $this->useForm($this->model_name . '_form');

    $this->setFormDatasource($CRUD_object);

    if( $this->request->hasPost() )
    {
      $CRUD_object->import($this->request);
      $CRUD_object->validate($this->error_list);

      if( $this->error_list->isEmpty() )
      {
        $CRUD_object->saveSkipValidation();
        $this->redirect();
      }
    }
  }

  protected function _findTemplateForAction($action)
  {
    parent :: _findTemplateForAction($action);

    if( !$this->view->getTemplate() )
    {
      $template_path = $this->name . '/' . $action . '.html';

      $wact_locator = lmbToolkit :: instance()->getWactLocator();

      if( $wact_locator->locateSourceTemplate($template_path))
        $this->setTemplate($template_path);
    }
  }
}

