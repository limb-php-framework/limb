<?php
lmb_require('limb/cms/src/model/lmbCmsUserRoles.class.php');

$editor = array(array('title' => 'Контент', 'icon' => '/shared/cms/images/icons/menu_content.png',  'children' => array(
  array(
    'title' => 'Текстовые страницы',
    'url' => '/admin_document',
    'icon' => '/shared/cms/images/icons/page.png',
  ),
  array(
    'title' => 'Текстовые блоки',
    'url' => '/admin_text_block',
    'icon' => '/shared/cms/images/icons/layout.png',
  ),
  array(
    'title' => 'Мета-данные (SEO)',
    'url' => '/admin_seo',
    'icon' => '/shared/cms/images/icons/page_white_stack.png',
  ),
)));

$only_admin = array(array('title' => 'Администрирование', 'icon' => '/shared/cms/images/icons/menu_service.png','children' => array(
  array(
    'title' => 'Пользователи',
    'url' => '/admin_user',
    'icon' => '/shared/cms/images/icons/user.png',
  ),
)));

$conf = array(
  lmbCmsUserRoles :: EDITOR  => $editor,
  lmbCmsUserRoles :: ADMIN  => array_merge_recursive($editor, $only_admin)
);

