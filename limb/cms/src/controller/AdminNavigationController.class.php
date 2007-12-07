<?php
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');

class AdminNavigationController extends AdminNodeWithObjectController
{
  protected $_object_class_name = 'lmbCmsNavigation';
  protected $_controller_name = 'admin_navigation';
  protected $_form_name = 'navigation_form';
}


