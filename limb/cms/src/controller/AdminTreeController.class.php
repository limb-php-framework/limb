<?php
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/cms/src/lmbCmsTreeBrowser.class.php');

class AdminTreeController extends lmbController
{
 function doCreateNode()
  {
    $this->useForm('node_form');
    $this->setFormDatasource($this->request);

    if($this->request->hasPost())
    {
      $class_name = $this->request->get('class_name') ? $this->request->get('class_name') : 'lmbCmsNode';
      $node = new $class_name();

      $this->_importAndSave($node);
    }
    else
      $this->request->set('class_name', 'lmbCmsNode');
  }

  function doEditNode()
  {
    $node = lmbActiveRecord :: findById('lmbCmsNode', $this->request->getInteger('id'));
    $this->useForm('node_form');
    $this->setFormDatasource($this->request);

    if($this->request->hasPost())
      $this->_importAndSave($node);
    else
    {
      $this->request->merge($node->export());
      $this->request->set('controller_name', $node->getControllerName());
    }
  }

  protected function _importAndSave($node)
  {
    $node->import($this->request);

    $node->validate($this->error_list);

    if($this->error_list->isValid())
    {
      $node->saveSkipValidation();
      $this->closePopup();
    }
  }

  function doDelete()
  {
    if($this->request->hasPost() && $this->request->get('delete'))
    {
      foreach($this->request->getArray('ids') as $id)
      {
        $node = lmbActiveRecord :: findById('lmbCmsNode', $id);
        $node->destroy();
      }
      $this->closePopup();
    }
  }

  function doSavePriority()
  {
    $priority = $this->request->get('priority');

    if(!is_array($priority) || !sizeof($priority))
      throw new lmbException('"priority" request param should be an array!');

    foreach($priority as $id => $value)
    {
      $node = new lmbCmsNode($id);
      $node->setPriority($value);
      $node->save();
    }

    $this->closePopup();
  }

  function doMove()
  {
    if($parent_id = $this->request->getInteger('id'))
    {
      $parent_node = new lmbCmsNode($parent_id);
      $this->request->set('parent', $parent_node);
    }

    $this->useForm('tree_form');
    $this->setFormDatasource($this->request);

    if($this->request->hasPost() && $this->request->get('move'))
    {
      $parent_id = $this->request->get('parent_id');
      foreach($this->request->getArray('ids') as $id)
      {
        $tree = lmbToolkit :: instance()->getCmsTree();
        $tree->moveNode($id, $parent_id);
      }
      $this->closePopup();
    }
  }

  function doProcessCommand()
  {
    $resource_type = $this->request->get('Type');
    $current_folder = $this->request->get('CurrentFolder');
    $command = $this->request->get('Command');

    $browser = new lmbCmsTreeBrowser();
    $browser->setCurrentFolderPath($current_folder);

    $this->_setXmlHeaders();

    $xml = 	'<?xml version="1.0" encoding="utf-8" ?>';
    $xml .= '<Connector command="' . $command . '" resourceType="' . $resource_type . '">' ;
    $xml .= '<CurrentFolder path="' . $current_folder . '" url="/" />' ;

    $xml .= '<Folders>' . $browser->renderFolders() . '</Folders>';
    $xml .= '<Files></Files>';

    $xml .= '</Connector>';

    return $xml;
  }

  protected function _setXmlHeaders()
  {
    $this->response->addHeader('Expires: Mon, 26 Jul 1997 05:00:00 GMT') ;
    $this->response->addHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT') ;
    $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate') ;
    $this->response->addHeader('Cache-Control: post-check=0, pre-check=0', false) ;
    $this->response->addHeader('Pragma: no-cache') ;
    $this->response->addHeader( 'Content-Type:text/xml; charset=utf-8' ) ;
  }
}


