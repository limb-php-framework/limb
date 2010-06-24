<?php
lmb_require('limb/cms/src/model/lmbCmsUserRoles.class.php');

$conf = array(
  lmbCmsUserRoles :: EDITOR  => array(
    'restricted_controllers' => array(
      'admin_user',
    ),

    'restricted_actions' => array(
      'admin_text_block' => array(
        'delete',
      ),
    )
  ),

  lmbCmsUserRoles :: ADMIN  => array(

  ),
);
