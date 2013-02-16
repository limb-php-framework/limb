# Шаг 3.4 Управление списком пользователей
Одна из основных функций пакета CMS, это панель управления. Попасть в нее можно по адресу http://sitename/admin (логин: admin, пароль: secret). На этом шаге мы построим CRUD-интерфейс для управления пользователями.

## Создание контроллера для панели управления
Для начала создадим контроллер для нашей страницы. Все контроллеры панели управления будут иметь префикс Admin - это позволяет использовать встроенный в пакет CMS функционал по ограничению доступа (см. lmbCmsAccessPolicyFilter).

Файл shop/src/controller/AdminUserController.class.php:

    <?php
    lmb_require('limb/cms/src/controller/lmbAdminObjectController.class.php');
 
    class AdminUserController extends lmbAdminObjectController
    {
      protected $_object_class_name = 'User';
    }

По суди единственное, что нам нужно сделать - указать класс, с которым работает контроллер. Остальная функциональность уже содержится в lmbAdminObjectController и работает на основе соглашений:

* Список объектов doDisplay():
  * коллекция объектов подставляется в шаблон в переменную 'items'
  * сортировка задается параметром 'sort' запроса, направление - параметром 'direction'
* Создание и редактирование объекта doCreate(), doEdit():
  * объект подставляется в шаблон в переменную 'item'
  * форма называется 'object_form' (перекрывается свойством $_form_name)
  * запрос является POST-запросом
  * все пришедшие данные импортируются в объект, как обычные строки
* Удаление объекта doDelete():
  * идентификаторы удаляемых объектов приходят в параметре 'ids'

## Шаблоны
Пожалуй, мы начнем с изучения шаблонов, которые будут использоваться на многих других шаблонах в панели управления и на фронтальной части. Это шаблоны:

* **pager.phtml** — пейджер для постраничного вывода (limb/cms/template/_admin/pager.phtml)
* **form_errors.phtml** — список ошибок валидации форм (limb/cms/template/_admin/form_errors.phtml)

Подробнее о тегах pager и paginate можно прочитать разделе [«Постраничный вывод данных в MACRO-шаблонах»](../../../../macro/docs/ru/macro/pagination.md).

### Шаблон списка пользователей

    {{wrap with="admin_page_layout.phtml" in="content_zone"}}
 
      {{include file="_admin/selectors.phtml"/}}
      {{include file="_admin_object/actions.phtml"/}}
 
      <div id="header">
        <h1>Users</h1>
        <div class="header_actions">
          {{apply template="object_action" action="create" is_link="true" title="Create user"/}}
        </div>
 
        {{include file='_admin/pager.phtml' items='{$this->items}'/}}
      </div>
 
      <div id="body">
 
        {{list using="{$#items}" as="$item" counter="$counter" parity='$parity'}}
 
          <div class='list'>
            <table>
              <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Login</th>
                <th>Email</th>
                <th>Address</th>
                <th>Actions</th>
              </tr>
 
              {{list:item}}
                <tr class="{$parity}">
                  <td>{$item.id}</td>
                  <td>{$item.name}</td>
                  <td>{$item.login}</td>
                  <td>{$item.email}</td>
                  <td>{$item.address|raw|nl2br}</td>
                  <td class='actions'>
                    {{apply template="object_action_edit" item="{$item}"/}}
                    {{apply template="object_action_delete" item="{$item}"/}}
                  </td>
                </tr>
              {{/list:item}}
              {{list:empty}}
                <div class="empty_list">Empty</div>
              {{/list:empty}}
            </table>
          </div>
 
        {{/list}}
 
      </div>
 
    {{/wrap}}

Поясним некоторые моменты:

* $#items (алиас к $this→items) добавлен в шаблон базовой реализацией экшена doDisplay(), описанной в файле **lmbAdminOjectController**.
* Чтобы не писать лишний раз пейджер для панели управления, мы воспользовались имеющимся в пакете CMS, передав туда коллекцию продуктов. Сам файл с пейджером (_admin/pager.phtml) мы подключили при помощи тега [{{include}}](../../../../macro/docs/ru/macro/tags/core_tags/include_tag.md).
* Для создания ссылок мы используем тег [{{route_url}}](../../../../macro/docs/ru/macro/tags/lmb_request_tags/lmb_route_url_tag.md)
* При помощи тега [{{list}}](../../../../macro/docs/ru/macro/tags/list_tags/list_tag.md) и [{{list:item}}](../../../../macro/docs/ru/macro/tags/list_tags/list_item_tag.md) выводятся списковые данные.
* Фильтр [raw](../../../../macro/docs/ru/macro/filters.md) в выражении {$item.address|raw|nl2br} применяется для того, чтобы отменить действие фильтра html, который применяет фунцию htmlspecialchars для значения выводимой переменной. Фильтр [nl2br](../../../../macro/docs/ru/macro/filters.md) используется для применения функции nl2br (new line to <br/>) к выводимой переменной {$item.description}.
* Для вывода возможных действий (редактирование и удаление) мы воспользовались тегом [{{apply}}](../../../../macro/docs/ru/macro/tags/core_tags/apply_tag.md). Использованные нами «куски» шаблонов можно посмотреть в файле **limb/cms/template/_admin_object/actions.phtml**. Основная их особенность в том, что вместо перезагрузки всей страницы, они выводят модальное окно.

Дополнительная информация о том, как данные из ActiveRecord-ов попадает в шаблоны доступна в разделе [Использование ACTIVE_RECORD в шаблонах MACRO](../../../../active_record/docs/ru/active_record/in_macro_templates.md). Предупреждение: некоторые вещи пока вам могут быть незнакомыми!

### Шаблоны для создания и редактирования пользователей

    {{wrap with="admin_modal_page_layout.phtml" into="content_zone"}}
      {{form id='object_form' name='user_form' method='post' enctype="multipart/form-data"}}
 
        <h1>Create user</h1>
 
        {{include file='_admin/form_errors.phtml'/}}
        {{include file='admin_user/include/form_fields.phtml'/}}
        {{include file='_admin/form_buttons.phtml'/}}
 
      {{/form}}
    {{/wrap}}

Обратите внимание, что в качестве базового шаблона мы используем не admin_page_layout, а admin_modal_page_layout, который адаптирован для вывода внутри модального окна.

Файл shop/admin_user/edit.phtml:

    {{wrap with="admin_modal_page_layout.phtml" into="content_zone"}}
      {{form id='object_form' name='object_form' method='post' enctype="multipart/form-data" runat='server'}}
 
        <h1>User &laquo;{$this->item.name}&raquo;</h1>
        {{include file='_admin/form_errors.phtml'/}}
        {{include file='admin_user/include/form_fields.phtml'/}}
        {{include file='_admin/form_buttons.phtml'/}}
 
      {{/form}}
    {{/wrap}}

Подробнее об тегах форм в разделе [«Теги форм и элементов форм»](../../../../macro/docs/ru/macro/form_tags.md).

Так как поля для обоих форм одинаковые, мы решили вынести их в общий шаблон **form_fields.phtml**

Файл shop/template/admin_user/include/form_fields.html:

    <div class='tabs'>
 
      <ul>
        <li id='properties'><a>Properties</a></li>
      </ul>
 
      <div id="tab_properties">
 
        {{include file="user/include/form_fields.phtml"/}}
 
        <dl class="field">
          <dt>{{label for="name"}}Password:{{/label}}</dt>
          <dd>{{input name='password' type='text' title='Password'/}}</dd>
        </dl>
 
      </div>
    </div>

Некоторые особенности форм на панели управления:

* Мы использовались автогенерацией вкладок. Элементы списка внутри div, с классом 'tabs', преобразуются во вкладки. Так, например, вкладка с id=properties

берет содержимое из div с id=tab_properties.

* вместо hashed_password у нас есть поле password, которое, как мы помним хэшируется перед сохранением объекта

### Добавление ссылки в навигацию
Чтобы добавить ссылку в панель создадим файл **shop/settings/navigation.conf.php** со следующим содержимым:

    <?php
    require_once('limb/cms/settings/navigation.conf.php');
    $conf[lmbCmsUserRoles :: ADMIN][0]['children'][] =
      array(
        "title" => "Users",
        "url" => "/admin_user/",
        "icon" => "/shared/cms/images/icons/user.png",
    );

## Далее
Следующий шаг — [«Шаг4. Создание и отображение списка товаров для администраторов»](./step4.md). Мы создадим для администраторов сайта список товаров с разделением на страницы, сделаем формы для создания нового товара и редактирования старых, реализуем действие для удаления товаров.
