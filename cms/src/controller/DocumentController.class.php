<?php
lmb_require('limb/cms/src/controller/lmbObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsDocument.class.php');
lmb_require('limb/cms/src/model/Seo.class.php');

class DocumentController extends lmbObjectController
{
  protected $_object_class_name = 'lmbCmsDocument';  
  
  function doItem()
  {
    if(!$this->item = $this->_getObjectByRequestedId())
      return $this->forwardTo404();
  
    $mod_date=intval($this->item->utime);
    $expires = 43200; // half of day
    $last_modified = gmdate('D, d M Y H:i:s', $mod_date) . ' GMT';
    $expire_date  = gmdate('D, d M Y H:i:s', gmmktime() + $expires) . ' GMT';

    $this->response->addHeader('Last-Modified: ' . $last_modified);
    $this->response->addHeader('Expires: '.$expire_date);
    $this->response->addHeader('Cache-Control: max-age='.$expires.', must-revalidate'); //half day

  }

}


