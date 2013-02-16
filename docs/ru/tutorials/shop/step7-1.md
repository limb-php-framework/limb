# Шаг 7.1. Модель связанная с заказами
## Связи между классами
Наша модель по хранению заказов будет выглядеть следующим образом:

![Alt-order_model](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:order_model.png)

Поясним:

* Order состоит из OrderLine. Здесь отношении композиции (один-ко-многим).
* OrderLine имеет ссылку на Product. На Product может ссылать множество OrderLine. Здесь у нас будет отнонаправленная связь один-ко-многим.
* User может иметь несколько Order, в тоже время Order имеет ссылку на User. Здесь двусторонее отношение один-ко-многим.

Что ж, не густо — только различные вариации один-ко-многим, однако нам этого будет достаточно, чтобы показать принципы.

Конечно, связи между объектами можно сохранять вручную — при помощи соответствующих ключей в таблицах базы данных, однако пакет ACTIVE_RECORD предоставляет средства, которые облегчают эту задачи и позволяют не отходить от объектной схемы работы.

## Поддержка отношений в пакете ACTIVE_RECORD
Пакет ACTIVE_RECORD содержит набор средств, которые позволяют автоматизировать работу с отношениями. Для этого необходимо лишь внутри требуемых классов добавить соответствующие описания отношений.

### Краткий пример
Начнем формировать отношения между Order и OrderLine, и на примере покажем как работать с отношениями при помощи пакета ACTIVE_RECORD. Создадим класс Order:

Файл shop/src/model/Order.class.php:

    <?php
    class Order extends lmbActiveRecord
    {
      protected $_has_many = array('lines' => array('field' => 'order_id',
                                                    'class' => 'OrderLine'));
 
    }

Обратите внимание на атрибут **$_has_many**. Это массив, который может содержать набор описаний отношений один-ко-многим данного класса с другими. **lines** — это название отношений, оно нам потребуется, когда мы будем добавлять позиции в заказ. Ключ **field** указывает на название поля в таблице, где хранятся экземпляры класса OrderLine, в которое будет сохранен идентификатор объекта класса Order. В нашем случае это поле order_id таблицы order_line. Ключ **class** указывает на класс, с которым Order состоит в отношении.

Теперь мы можем написать следующий код:

    $order = new Order();
    $item1 = new OrderLine();
    $order->addToLines($item1);
    // или
    $lines = $order->getLines(); // lines - это коллекция объектов, которая поддерживает интерфейс Iterator.
    $lines->add($line1);
    //или так
    $lines = $order->get('lines'); // это очень важно! Будет нами использоваться в WACT-шаблонах
    $lines->add($line1);
    // или 
    $order->setLines(array($line1));

Поддержка методов вида addToLines(), getLines(), setLines() добавляется в ActiveRecord автоматически после создания соответствующего описания отношения. Обратите внимание на поддержку $order→get('lines'); - это позволит нам легко получить список позиций, связанных с заказом, прямо из шаблона.

Связанные объекты сохраняются в базе данных и загружаются из базы данных автоматически, например:

    $order = new Order();
    $item1 = new OrderLine();
    $item2 = new OrderLine();
    $order->addToLines($item1);
    $order->addToLines($item2);
    $order->save(); // будет сохранен Order и связанные с ним item1 и item2
    //....
    $order2 = new Order($order->getId());
    $lines = $order2->getLines(); // lines будет содержать объекты с данными item1 и item2

Пока класс OrderLine ничего не знает об Order. Добавим описание отношения в класса Order. Оно будет немного отличаться:

    <?php
    class OrderLine extends lmbActiveRecord
    {
      protected $_many_belongs_to = array('order' => array('field' => 'order_id',
                                                           'class' => 'Order'));
 
      [...]
    }

Атрибут **$_many_belongs_to** описывает отношение много-к-одному (то есть один-ко-многим с другой стороны). Теперь ключ **field** указывает на название поля в таблице, где хранятся объекты класса OrderLine. **class** описывает название класса, с которым OrderLine находится к отношении много-к-одному.

Теперь мы можем использовать следующий код:

    $order_line = new OrderLine(previously_saved_id);
    $order = $order_line->getOrder();
    // или
    $order = $order_line->get('order');

В целом, принцип должен быть понятен.

Обратите внимание, что при удалении объекта Order будут также удалены соответсвующие объекты OrderLine (но не наоборот).

Возможности пакета ACTIVE_RECORD по работе с отношениями

Мы не будем описывать здесь все, что касается работы с отношениями, так как это достаточно обширная тема. Приведем лишь список наиболее значительных моментов, которые вам рекомендуется знать.

Полный список возможностей пакета ACTIVE_RECORD вы можете узнать из [этого списка](../../../../active_record/docs/ru/active_record.md).

Наше приложение будет пользоваться только базовыми средствами пакета ACTIVE_RECORD по работе со связанными объектами. Остальное, мы надеемся, вы сможете освоить при помощи раздела [«Использование пакета ACTIVE_RECORD»](../../../../active_record/docs/ru/active_record.md).

## Классы OrderLine, Order, User и отношения между ними
У нас уже есть начальная реализация OrderLine, теперь мы добавим к нее описания связи с Product:

Файл shop/src/model/OrderLine.class.php:

    <?php
    class OrderLine extends lmbActiveRecord
    {
      protected $_many_belongs_to = array('order' => array('field' => 'order_id',
                                                           'class' => 'Order'),
                                          'product' => array('field' => 'product_id',
                                                             'class' => 'Product'));
      [...]
    }

(Теоретически, мы могли бы описать связь OrderLine и Product как один-ко-одному)

Пользователи у нас должны иметь также связь с заказами, поэтому добавим в соответсвующие классы описания этого отношения.

Файл shop/src/model/User.class.php:

    <?php
    class User extends lmbActiveRecord
    {
      protected $_has_many = array('orders' => array('field' => 'user_id',
                                                     'class' => 'Order',
                                                     'sort_params' => array('date' => 'DESC')));
      [...]
    }

Обратите внимание на ключ **sort_params**. В качестве значения — массив пар 'поле' ⇒ 'тип сортировки'. Это позволяет задать способ сортировки связанных с пользователем заказов по-умолчанию.

Файл shop/src/model/Order.class.php:

    <?php
    class Order extends lmbActiveRecord
    {
      protected $_has_many = array('lines' => array('field' => 'order_id',
                                                    'class' => 'OrderLine'));
 
      protected $_many_belongs_to = array('user' => array('field' => 'user_id',
                                                          'class' => 'User'));
    }

## Класс Order
Теперь доработаем класс Order следующим образом:

* Добавим поддержку статусов заказа
* Добавим фабричный метод, который будет создавать новый объект Order из Cart.

Файл shop/src/model/Order.class.php:

    class Order extends lmbActiveRecord
    {
      const STATUS_NEW                 = 1;
      const STATUS_PROCESSED           = 2;
      const STATUS_FINISHED            = 3;
 
      protected $_has_many = array('lines' => array('field' => 'order_id',
                                                    'class' => 'OrderLine'));
 
      protected $_many_belongs_to = array('user' => array('field' => 'user_id',
                                                          'class' => 'User'));
 
      function createForCart($cart)
      {
        $order = new Order();
        $order->setStatus(Order :: STATUS_NEW);
        $order->setLines($cart->getItems());
        $order->setSumm($cart->getTotalSumm());
        $order->setDate(time());
        return $order;
      }
 
      function setStatus($value)
      {
        $statuses = $this->getStatusOptions();
        if(isset($statuses[$value]))
          $this->_setRaw('status', $value);
      }
 
      function getStatusName()
      {
        $statuses = $this->getStatusOptions();
        return $statuses[$this->getStatus()];
      }
 
      function getStatusOptions()
      {
        return array(
      	  self :: STATUS_NEW => 'new',
      	  self :: STATUS_PROCESSED => 'processed',
      	  self :: STATUS_FINISHED => 'finished'
      	);
      }
    }

Поясним некоторые моменты.

Мы добавили набор контант, которыми мы будем пользоваться в коде для указания текущего статуса заказа. Это обычный прием, который мы используем в таких случаях. Если статусов было бы много и(или) они описывались бы не одним, а несколькими полями, мы бы вынесли отдельный класс OrderStatus.

Особо стоит отметить метод Order :: **setStatus($status)**. Обратите внимание на вызов метода **_setRaw('status', $value)**. Нам нельзя в данном методе использовать set('status', $value), так как lmbObject (от которого отнаследован lmbActiveRecord) автоматически вызовет метод setStatus(), и это приведет к рекурсии. _setRaw() нужен как раз в таких случаях - он устанавливает значение поля напрямую, минуя проверку, если ли метод вида setStatus() или нет.

(Кстати, мы могли бы вообще обойтись без использования класса Cart, а использовать только класс Order).

## Далее
Теперь, когда наша модель готова, можно добавить функционал по оформлению корзины.

Итак, следующий шаг:[ Шаг 7.2. Отправка заказа](./step7-2.md).
