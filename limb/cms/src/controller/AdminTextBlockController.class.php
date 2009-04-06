<?php
lmb_require('limb/cms/src/controller/lmbAdminObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsTextBlock.class.php');

class AdminTextBlockController extends lmbAdminObjectController
{
  protected $_form_name = 'object_form';
  protected $_object_class_name = 'lmbCmsTextBlock';

  public function doDisplay()
  {
    $this->blocks = $this->_getBlocks();
  }

  protected function _getBlocks()
  {
    $blocks = lmbCollection::toFlatArray(lmbActiveRecord::find('lmbCmsTextBlock'), 'identifier');

    $result = array();
    foreach($this->toolkit->getConf('text_blocks') as $identifier => $default_properties)
    {
      if(isset($blocks[$identifier]))
      {
        $item = $blocks[$identifier];
        $item['exists'] = true;
      }
      else
      {
        $item = new lmbCmsTextBlock();
        $item->import($default_properties);
        $item->setIdentifier($identifier);
        $item['exists'] = false;
      }

      $result[$identifier] = $item;
    }

    return $result;
  }

  public function doEdit()
  {
    if(!$this->item = lmbCmsTextBlock::findOneByIdentifier($this->request->get('id')))
      $this->forwardTo404();

    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->item);

    if(!$this->request->hasPost())
      return;

    $this->_import();
    $this->_validateAndSave(false);
  }

  function doPreview()
  {
    if(!$this->item = lmbCmsTextBlock::findOneByIdentifier($this->request->get('id')))
      $this->forwardTo404();
  }
}


