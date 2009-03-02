<?php
lmb_require('limb/cms/src/controller/lmbAdminObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsTextBlock.class.php');

class AdminTextBlockController extends lmbAdminObjectController
{
  protected $_form_name = 'object_form';
  protected $_object_class_name = 'lmbCmsTextBlock';
}

