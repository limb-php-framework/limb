<?php
lmb_require('limb/cms/src/model/lmbCmsUserRoles.class.php');

$editor = array(array('title' => 'Контент',
                      'id' => 'content',
                           'children' => array(
                              array('title' => 'Текстовые страницы', 'url' => '/admin_documents'),
                              array('title' => 'Навигация', 'url' => '/admin_navigation'),
                              )),
  );

$admin = array(array('title' => 'Администрирование',
                     'id' => 'item1',
                            'children' => array(
                               array('title' => 'Пользователи', 'url' => '/admin_user'),
                               array('title' => 'Структура сайта', 'url' => '/admin_tree'),
                               )));

$conf = array(
  lmbCmsUserRoles :: EDITOR  => $editor,
  lmbCmsUserRoles :: ADMIN  => $admin,
);

