# Пакет CMS
Основная функции пакета — разработка административного web-интерфейса приложения. Помимо этого пакет предоставляет возможности по созданию и выводу страниц и текстовых блоков.

## Установка
Пакет базируется на функционале WEB_APP, и представляет из себя набор моделей, контроллеров, шаблонов и сервисов. Самый простой способ создать приложение - воспользоваться утилитой limb.php:

    limb project_create

Для ручного создания приложения на базе пакета CMS воспользуйтесь [инструкцией](../../../docs/ru/cms_manual_setup.md).

## Текстовые страницы
Данный функционал позволяет создавать и управлять отдельными страницами вашего сайта.

Текст страницы вписывает в шаблон template/document/item.phtlm, который может быть перекрыт в вашем приложении.

## Текстовые блоки

Текстовые блоки представляют из себя куски текста, которые можно редактировать из административного интерфейса, а потом легко вставлять в любое место вашего сайта.

Блоки объявляются в конфигурационном файле **text_blocks.conf.php**:

    <?php
    $conf = array(
      'footer' => array('title' => 'Подвал Сайта',
                        'content' => '<p>Подвал</p>'),
    );

Поле **content** это первоначальное содержимое блока. После редактирования содержимое будет храниться и извлекаться из базы данных, а не из конфигурационного файла.

Получить содержимое блоков можно следующим образом:

    echo lmbCmsTextBlock::getRawContent('footer');

## Расширение административного интерфейса
Для автоматической генерации административного интерфейса вы можете воспользоваться пакетом [CONSTRUCTOR](../../../constructor/docs/ru/constructor.md).

Все контроллеры, имена которых начинаются с **admin** считаются частью административного интерфейса, и проверяют пользователя на «залогиненность» (см. lmbCmsAccessPolicyFilter)

Разберем на примере добавления новостей на сайт:

### Схема базы
Для начала создадим таблицу **news**:

    CREATE TABLE  `skel`.`news` (
      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
      `title` VARCHAR(255) NOT NULL DEFAULT '',
      `content` longtext NOT NULL,
      `ctime` INT(11) NOT NULL,
      PRIMARY KEY (`id`)
    );

### Создание модели и контроллера
Для создания контроллеров, типичных для CRUD'ов, удобнее всего воспользоваться классом lmbAdminObjectController, в качестве базового. Для этого сначала создадим модель (файл /src/model/News.class.php):

    <?php
    lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
 
    class News extends lmbActiveRecord {}

и контроллер (файл /src/controller/AdminNewsController.class.php)

    <?php
    lmb_require('limb/cms/src/controller/lmbAdminObjectController.class.php');
    lmb_require('src/model/News.class.php');
 
    class AdminNewsController extends lmbAdminObjectController
    {
      protected $_object_class_name = 'News';
    }

### Создание шаблонов
Добавим шаблоны для отображения списка новостей (файл template/admin_news/display.phtml):

    {{wrap with="admin_page_layout.phtml" into="content_zone"}}
        <div id="header">
          <h1>Новости</h1>
          <div class="header_actions">
            <a href='{{route_url params="controller:admin_news,action:create"/}}' title="Добавить элемент" class='thickbox'><img src='/shared/cms/images/icons/page_white_add.png'/> Добавить новость</a>
          </div>
        </div>
 
        <div id="body">
          {{list using='{$#items}' parity='{$parity}'}}
          {{include file="_admin_object/actions.phtml"/}}
          <div class="list">
            <table>
              <tr>
                <th>#ID</th><th>Заголовок</th><th>Дата</th><th>Действия</th>
              </tr>
              {{list:item}}
                <tr class='{$parity}'>
                  <td>#{$item.id}</td><td>{$item.title}</td><td>{$item.ctime|date:'Y'}</td>
                  <td class='actions'>
                    {{apply template="object_action_edit" item="{$item}" icon="page_white_edit" /}}
                    {{apply template="object_action_delete" item="{$item}" icon="page_white_delete" /}}
                  </td>
                </tr>
                {{/list:item}}          
              </table>
            </div>
          {{/list}}
        </div>
    {{/wrap}}

шаблон формы (файл template/admin_news/include/form_fields.phtml)

    {{include file='_admin/form_errors.phtml'/}}
    <div class='tabs'>
 
      <ul>
        <li id='properties'><a href="#">Свойства</a></li>
      </ul>
 
      <div id="tab_properties">
        <dl class="required field">
          <dt><label for="title">Заголовок:</label></dt>
          <dd>{{input type="text" name="title" id="title" title="Заголовок"/}}</dd>
        </dl>
        <dl class="field">
          <dt>{{label for="content"}}Текст:{{/label}}</dt>
          <dd>{{textarea name="content" id="content" title="Текст"/}}</dd>
        </dl>
      </div>
 
    </div>
    {{include file='_admin/form_buttons.phtml'/}}

шаблоны для создания и редактирования новости (файлы template/admin_news/create.phtml и template/admin_news/edit.phtml)

    {{wrap with="admin_modal_page_layout.phtml"}}
      {{into slot='content_zone'}}
        {{form id='object_form' name='user_form' method='post' enctype="multipart/form-data"}}
          <h1>Добавление новости</h1>
          {{include file='admin_news/include/form_fields.phtml'/}}
        {{/form}}
      {{/into}}
    {{/wrap}}

    {{wrap with="admin_modal_page_layout.phtml" into="content_zone"}}
      <h1>Редактирование записи {$#item.title}</h1>
      {{form id='object_form' name='object_form' method='post' enctype="multipart/form-data"}}
        {{include file='admin_news/include/form_fields.phtml'/}}
      {{/form}}
    {{/wrap}}

### Добавление пункта в меню
Для того, чтобы расширить стандартное меню необходимо добавить необходимую опцию в navigation-конфиг. Для этого создадим в нашем проекте файл navigation.conf.php, со следующим содержимым:

    <?php
    require_once('limb/cms/settings/navigation.conf.php');
 
    $conf[lmbCmsUserRoles::ADMIN][0]['children'][] = array(
        'title' => 'Новости',
        'url' => '/admin_news',
        'icon' => '/shared/cms/images/icons/page.png',
      );
