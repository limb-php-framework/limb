# Шаг 8. Работа с заказами
Итак, наши пользователи могут отсылать заказы. Теперь наша задача — расширить панель управления так, чтобы администраторы могли просматривать список заказов, а пользователи - просматривать сделанные ими заказы.

Администратор должен иметь возможность:

* просмотреть все заказы,
* просмотреть только заказы с определенным статусом (новый, в обработке, выполненный)
* просмотреть информацию по заказу
* сменить статус заказа

## Просмотр и изменение статусов заказов администраторами
### Шаблон admin_order/display.phtml
При помощи этого шаблона мы будет выводить список заказов. Страница будет также содержать небольшую форму, которая позволит указать статус заказов, которые нужно отобразить. Для вывода списка заказов, мы будем использовать статический find()-метод Order :: findForAdmin(), который мы добавим чуть позже. В принпипе, аналогичную задачу мы решали при выводе списка товаров на фронтальной части для покупателей с возможностью поиска и фильтрации.

Файл shop/template/admin_order/display.phtml:

    <? $this->title = 'Orders'; ?>
    {{wrap with="admin_page_layout.phtml" in="content_zone"}}
 
    <p>
    {{form method="GET" id='filter_form'}}
      <?php $statuses = Order :: getStatusOptions(); ?>
      Filter : {{select id='status' name='status' options="$statuses"}}
      {{option value='' prepend='true'}}Show all{{/option}}{{/select}}
      <input type='submit' name='filter' value="Filter" class='button'/><br/>
    {{/form}}
    </p>
 
    {{include file="_admin/selectors.phtml"/}}
    {{include file="_admin_object/actions.phtml"/}}
 
    {{include file='_admin/pager.phtml' items="$#orders"/}}
 
    <div id="body">
      {{list using="$#orders" parity="$parity"}}
        <div class='list'>
          <table>
            <tr>
              <th>User</th>
              <th>Date</th>
              <th>Status</th>
              <th>Summ</th>
              <th>Action</th>
            </tr>
 
            {{list:item}}
              <tr class="{$parity}">
                <td>{$item.user.login}</td>
                <td>{$item.date|date:"d.m.Y"}</td>
                <td>{$item.status_name}</td>
                <td>{$item.summ}</td>
                <td class='actions'>
                  {{apply template="object_action" action="details" title="Details" icon="zoom" item="{$item}"/}}
                  {{apply template="object_action_edit" item="{$item}"/}}
                  {{apply template="object_action_delete" item="{$item}"/}}
                </td>
              </tr>
            {{/list:item}}
            {{list:empty}}
              <div class="empty_list">We have no orders yet.</div>
            {{/list:empty}}
          </table>
        </div>
      {{/list}}
    </div>
    {{/wrap}}

Обратите внимание вот на эти две строки:

    <?php $statuses = Order :: getStatusOptions(); ?>
    Filter : {{select id='status' name='status' options="$statuses"}}{{option value='' prepend='true'}}Show all{{/option}}{{/select}}

При помощи php-вставки мы передаем набор данных в тег [{{select}}](../../../../macro/docs/ru/macro/tags/form_tags/select_tag.md). Этот тег используется для заполнения списка опций тега <select>.

При помощи выражения **{$item.date|date:«d.m.Y H:i»}** мы вывели дату оформления заказа. Примененный фильтр [date](../../../../macro/docs/ru/macro/filters.md) позволил явно указать формат вывода даты.

Мы использовали код:

    {{apply template="object_action" action="details" title="Details" icon="zoom" item="{$item}"/}}

для того чтобы создать кнопку, открывающую окно с детальной информацией конкретного заказа.

### Изменения в классе Order
Добавим метод findForAdmin() в класс Order.

Файл shop/src/model/Order.class.php:

    <?php
    class Order extends lmbActiveRecord
    {
      [...]
      static function findForAdmin($params = array())
      {
        $criteria = new lmbSQLCriteria();
 
        if(isset($params['status']))
          $criteria->add(lmbSQLCriteria::equal('status', $params['status']));
 
        return Order :: find($criteria);
      }
      [...]
    }

## Шаблон admin_order/details.phtml
При помощи этого шаблона мы будем отображать содержимое конкретного заказа, а также дадим администраторам возможность устанавливать новый статус заказа:

Файл shop/template/admin_order/details.phtml:

    <? $this->title ='Order #'.$this->order->getId(); ?>
    {{wrap with="admin_modal_page_layout.phtml" in="content_zone"}}
 
    {{form id='status_form' name='status_form' method='post'}}
      <h1>Order #{$#order.id}</h1>
 
      <dl class="field">
        <dt>Items:</dt>
        <dd>
        {{list using='$#order.lines' parity='$parity' as='$line'}}
        <table cellpadding="0" cellspacing="0" class='list'>
          <thead>
            <tr>
              <th>Title</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Summ</th>
            </tr>
          </thead>
          {{list:item}}
            <tr class='{$parity}'>
             <td>{$line.product.title}</td>
             <td>${$line.price|number:2, '.', ' '}</td>
             <td>{$line.quantity}</td>
             <td>${$line.summ|number:2, '.', ' '}</td>
            </tr>
          {{/list:item}}
        </table>
      {{/list}}
        </dd>
      </dl>
 
      <dl class="field">
        <dt>Customer:</dt>
        <dd><a href='mailto:{$#order.user.email}'>{$#order.user.name} ({$#order.user.login})</a></dd>
      </dl>
 
      <dl class="field">
        <dt>Address :</dt>
        <dd>{$#order.address}</dd>
      </dl>
 
      <dl class="field">
    	  <dt>Status :</dt>
    	  <dd><?php $statuses = Order :: getStatusOptions(); ?>
    	    {{select id='status' name='status' options="$statuses"/}}
    	  </dd>
    	</dl>
 
      {{include file='_admin/form_buttons.phtml'/}}
 
    {{/form}}
    {{/wrap}}

Шаблон содержит форму, которая позволит администраторам менять статус заказа.

### Контроллер AdminOrderController

    <?php
    class AdminOrderController extends lmbController
    {
      [...]
 
      function doDetails()
      {
        try
        {
          $this->order = new Order($this->request->getInteger('id'));
          $this->useForm('status_form');
          $this->setFormDatasource($this->order);
        }
        catch(lmbARException $e)
        {
            $this->_endDialog();
          $this->flashError('Wrond Order ID');
          return;
        }
     
        if(!$this->request->hasPost())
          return;
 
        $status = $this->request->getInteger('status');
        $this->order->setStatus($status);
        $this->order->save();
 
        $this->_endDialog();
 
        $this->flashMessage('Order status was changed to "'.$this->order->getStatusName().'"');
      }  
    }

Надеемся, что все в данном классе для вас уже знакомо и пояснений не требует.

И последнее для отображения надо добавить путь к созданному нами контроллеру в файл навигации административной панели:

Файл shop/settings/navigation.conf.php:

    [...]
    $conf[lmbCmsUserRoles :: ADMIN][0]['children'][] =
      array(
        "title" => "Orders",
        "url" => "/admin_order/",
        "icon" => "/shared/cms/images/icons/money.png",
    );

## Предварительные итоги
Покажем внешний вид страницы списка заказов в панеле управления:

![Alt-orders](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:orders.png)

и страницы конкретного заказа:

![Alt-single_order](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:single_order.png)

## Просмотр заказов покупателями
Последний небольшой шаг — добавление возможности покупателям просматривать свои заказы.

Для этого мы добавим 2 шаблона user/orders.html и user/show_order.html и добавим метод doShowOrder(), где мы будем проверять, имеет ли пользователь право просматривать определенный заказ.

### Шаблон user/orders.phtml
Файл shop/template/user/orders.phtml:

    <? $this->title ='Your orders'; ?>
    {{wrap with="front_page_layout.phtml" in="content_zone"}}
 
    {{list using='$#toolkit.user.orders'}}
      <table cellpadding="0" cellspacing="0" class='list'>
    	  <thead>
    	  <tr>
    	    <th>#</th>
    	    <th>Date</th>
    	    <th>Summ</th>
    	    <th>Status</th>
    	  </tr>
    	  </thead>
    	  {{list:item}}
    	  <tr>
    	   <td><a href="{{route_url params='action:show_order,id:{$item.id}'}}">{$item.id}</a></td>
    	   <td><a href="{{route_url params='action:show_order,id:{$item.id}'}}">{$item.date|date:"F j, Y, G:i"}</a></td>
    	   <td>${$item.summ|number:2, '.', ' '}</td>
    	   <td>{$item.status_name}</td>
    	  </tr>
    	  {{/list:item}}
    	</table>
      {{list:empty}}
        You made no orders in our shop yet.
      {{/list:empty}}
    {{/list}}
    {{/wrap}}

Так как объект toolkit всегда доступен в корне нашего MACRO шаблона, мы можем получить список заказов пользователя при помощи конструкции using='$#toolkit.user.orders'. Помните, мы добавили в класс User связь с заказами один-ко-многим под названием orders. Именно это и позволяет нам использовать подобную конструкцию прямо в шаблоне.

### Шаблон profile/show_order.html
Мы решили реализовать получение данных в данном случае обычным для большинства разработчиков способом - передать их из контроллера. Было бы слишком небезопасно давать возможность пользователям иметь возможность просмотреть содержимого любого заказа, ведь идентификатор можно легко изменить.

Файл shop/template/user/show_order.html:

    <? $this->title ='Order details'; ?>
 
    {{wrap with="front_page_layout.phtml" in="content_zone"}}
 
    {{list using='$#order.lines' parity="$parity"}}
    <table cellpadding="0" cellspacing="0" class='list'>
      <thead>
      <tr>
        <th>Title</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Summ</th>
      </tr>
      </thead>
      {{list:item}}
      <tr class='{$parity}'>
       <td>{$item.product.title}</td>
       <td>${$item.price|number:2, '.', ' '}</td>
       <td>{$item.quantity}</td>
       <td>${$item.summ|number:2, '.', ' '}</td>
      </tr>
      {{/list:item}}
    </table>
    {{/list}}
    {{/wrap}}

Конструкция **{{list using='$#order.lines'}}** заберет из корневого класса MACRО шаблона переменную order, которую мы поставим в шаблон из контроллера чуть ниже, затем из order возьмет переменную **lines**. **lines** — это коллекция позиций заказа.

## Изменения в контроллере UserController
Добавим в UserController метод doShowOrder(), который будет загружать указанный заказ и проверять право доступа текущего пользователя к заказу:

    <?php
    class UserController extends lmbController
    {
      [...]
      function doShowOrder()
      {
        try
        {
          $order = new Order($this->request->getInteger('id'));
          if(!$order->belongsToUser($this->toolkit->getUser()))
            $this->flashAndRedirect('You can see only your orders!', '/');
          else
            $this->order = $order;
        }
        catch(lmbARException $e)
        {
          $this->flashAndRerdirect('Can\'t load order!', '/');
        }
      }
      [...]
    }

### Изменения в классе Order
Теперь необходимо только добавить метод Order :: belongsToUser().

    class Order extends lmbActiveRecord
    {
      [...]
      function belongsToUser($user)
      {
        return ($this->getUserId() == $user->getId());
      }
      [...]
    }

## Далее
Это все, о чем мы хотели рассказать Вам о Limb в рамках данного примера.

У нас есть для Ваc еще один шаг: [Шаг 9. Рекомендации по дальнейшему изучению](./step9.md).
