<?php
lmb_require('limb/cms/src/model/lmbCmsUserRoles.class.php');
$toolkit = lmbToolkit :: instance();

$conf = array(
  lmbCmsUserRoles :: EDITOR  => array('allowed_controllers' => array(
                                                             'admin_documents',
                                                             'admin_navigation',
                                                             'admin_tree'),
                                      'restricted_actions' => array('admin_tree' =>
                                                                    array('display', 'move', 'delete', 'create', 'edit'))
                            ),

  lmbCmsUserRoles :: ADMIN  => array('allowed_controllers' => array('admin_user',
                                                                    'admin_tree')),

  'allowed_controllers' => array('admin'),


 );
?>
