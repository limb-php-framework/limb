<?php
lmb_require('limb/web_app/src/controller/lmbController.class.php');

class DocumentController extends lmbController
{
  function doDisplay()
  {
    if(!$this->node = lmbCmsNode :: findRequested())
      return $this->forwardTo404();

    $this->title = $this->node->getTitle();
  }
}

?>
