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
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');

class AdminDocumentsController extends AdminNodeWithObjectController
{
  protected $_object_class_name = 'lmbCmsDocument';
  protected $_controller_name = 'document';
  protected $_form_name = 'document_form';

  function doDisplay()
  {
    if(!$this->request->hasPost())
    {
      if($parent_id = $this->request->get('id'))
      {
        if(!$parent = lmbCmsNode :: findById($parent_id))
          return;

        $this->passToView('parent_path', $parent->getAbsoluteUrlPath());
      }
      else
        $this->passToView('parent_path', '/');
    }
    else
    {
      if($parent_path = $this->request->get('parent_path'))
      {
        if(!$node = lmbCmsNode :: findByPath($parent_path))
          return;

        $this->redirect(array('id' => $node->getId()));
      }
    }
  }

  function _initCreateForm()
  {
    if($parent_id = $this->request->get('parent'))
    {
      if(!$parent = lmbCmsNode :: findById($parent_id))
        return;

      $this->passToView('parent_path', $parent->getAbsoluteUrlPath());
    }
  }

  function _initEditForm()
  {
    parent :: _initEditForm();
    $this->passToView('parent_path', $this->node->getParent()->getAbsoluteUrlPath());
  }

  function _onBeforeSave()
  {
    if($parent_path = $this->request->get('parent_path'))
    {
      if(!$parent = lmbCmsNode :: findByPath($parent_path))
        return;

      $this->node->setParent($parent);
    }
  }

  function doPublish()
  {
    $this->performPublishCommand();
  }

  function doUnpublish()
  {
    $this->performUnpublishCommand();
  }
}


