# Шаг 6. Работа покупателей с корзиной заказа
Общая схема этого шага будет выглядеть следующим образом:

* Корзина будет представлена классом **Cart**, который будет отнаследован от класса **lmbObject**. Это наследование позволит использовать get-методы для получения данных при выводе содержимого корзины в шаблоне (зачем, будет пояснено чуть позже).
* Объект корзины мы будем хранить в сессии.
* Корзина будет содержать несохраненные экземпляры класса **OrderLine** (до момента отправки заказа)
* OrderLine будет содержать фабричный метод (factory method), который будет создавать объект OrderLine из объекта класса Product.
* Все управление корзиной будет осуществляться через контроллер **CartController**

## Классы модели для работы с корзиной
### Класс OrderLine
Мы уже создали таблицу order_line в базе данных нашего приложения. Теперь нам потребуется класс OrderLine, объекты которого мы будем хранить в корзине.

    <?php
    class OrderLine extends lmbActiveRecord
    {
      static function createForProduct($product)
      {
        $line = new OrderLine();
        $line->setProduct($product);
        $line->setQuantity(0);
        $line->setPrice($product->getPrice());
        return $line;
      }
 
      function increaseQuantity($amount = 1)
      {
        $this->setQuantity($this->getQuantity() + $amount);
      } 
 
      function getSumm()
      {
        return $this->getQuantity() * $this->getPrice();
      }
    }

Когда пользователь будет добавлять товары в корзину, он будет указывать идентификатор товара Product. Нам необходимо будет создавать экземпляр OrderLine из объекта Product. Для этого мы добавим фабричный метод в класс OrderLine:

При создании OrderLine мы копируем цену товара. Это сделано для того, чтобы в будущем при изменении цены на товар, суммы наших прошлых заказов никак не менялись.

Когда покупатель будет добавлять один и тот же товар в корзину дважды, мы не будем создавать экземпляры OrderLine — вместо этого мы будем только увеличивать количество единиц товара. Для этого мы ввели метод OrderLine :: increaseQuantity($amount):

Ну и последний штрих — это метод getSumm(), который возвращает сумму за эту позицию заказа.

Пока с OrderLine мы закончили, но обязательно вернемся, когда дойдем по оформления заказа покупателя.

### Класс Cart
Класс Cart будет содержать список OrderLine, а также логику по добавлению элементов:

Файл shop/src/model/Cart.class.php:

    <?php
    lmb_require('limb/core/src/lmbObject.class.php');
    lmb_require('src/model/OrderLine.class.php');
 
    class Cart extends lmbObject
    {
      protected $line_items = array();
 
      function addProduct($product)
      {
        $id = $product->getId();
 
        if(!isset($this->line_items[$id]))
          $this->line_items[$id] = OrderLine :: createForProduct($product);
 
        $this->line_items[$id]->increaseQuantity(1);
      }
 
      function getItems()
      {
        return $this->line_items;
      }
 
      function reset()
      {
        parent :: reset();
 
        $this->line_items = array();
      }
    }
    
Для очистки корзины можно будет пользоваться методом Cart :: reset(), этот метод находится в классе lmbObject, но мы его переопределим вызвав метод родительского класса.

Скорее всего, нам потребуются методы для получения общей суммы и количества товарных позиций в корзине. Поэтому добавим методы getTotalSumm() и getItemsCount():

    class Cart extends lmbObject
    {
      [...]
      function getTotalSumm()
      {
        $result = 0;
        foreach($this->line_items as $item)
          $result += $item->getSumm();
        return $result;
      }
 
      function getItemsCount()
      {
        return sizeof($this->line_items);
      }
    }
    
Этого вполне достаточно. Теперь можно перейти к контроллеру CartController.

## Контроллер CartController. Хранение корзины в сессии

Создадим самую базовую реализацию CartController. Контроллер будет содержать метод для инициализации корзины и получения ее из сессии, а также методы для действий:

* doAdd() — добавление товара в корзину
* doDisplay() — отображение содержимого товара
* doEmpty() — полная очистка корзины

Файл shop/src/controller/CartController.class.php:

    <?php
 
    class CartController extends lmbController
    {
      function doDisplay()
      {
        $this->view->set('cart', $this->_getCart());
      }
 
      function doEmpty()
      {
        $cart = $this->_getCart();
        $cart->reset();
        $this->redirect();
      }
 
      function doAdd()
      {
        $product_id = $this->request->getInteger('id');
        try
        {
          $product = Product :: findById($product_id);
          $cart = $this->_getCart();
          $cart->addProduct($product);
          $this->flashMessage('Product "' . $product->getTitle() . '" added to your cart!');
        }
        catch(lmbARException $e)
        {
          $this->flashError('Wrong product!');
        }
 
        if(isset($_SERVER['HTTP_REFERER']))
          $this->redirect($_SERVER['HTTP_REFERER']);
        else
          $this->redirect();
      }
 
      protected function _getCart()
      {
        $session = $this->toolkit->getSession();
        if(!$cart = $session->get('cart'))
        {
          $cart = new Cart();
          $session->set('cart', $cart);
        }
 
        return $cart;
      }
    }

Поясним некоторые моменты.

    [...]
      try
      {
        $product = lmbActiveRecord :: findById('Product', $product_id);
        $cart = $this->_getCart();
        $cart->addProduct($product);
        $this->flashMessage('Product "' . $product->getTitle() . '" added to your cart!');
      }
      catch(lmbARException $e)
      {
        $this->flashError('Wrong product!');
      }

lmbActiveRecord :: findById генерирует исключение, если объект с указанным идентификатором загрузить не удалось. Чтобы не отображать фатальную ошибку, мы ловим это исключение (класса lmbARException) и передаем в шаблон сообщение об ошибке ( **flashError()** ). Если продукт был добавлен в корзину нормально, тогда мы передаем в шаблон информационное сообщение ( **flashMessage()** ).

## Изменения в шаблонах
### Изменения в product/display.phtml
Для того, чтобы добавить товар в корзину нам для начала нужно добавить ссылку на это действие в шаблон product/display.phtml:

    [...]
              Price:<b>${$item.price|number:2, '.', ' '}</b><br/>
              <a href="{{route_url params='controller:cart,action:add,id:{$item.id}'}}">Add to cart</a><br/>
    [...]

### Шаблон cart/display.phtml для отображения содержимого корзины
Файл shop/template/cart/display.phtml:

    <? $this->title ='Your Cart'; ?>
    {{wrap with="front_page_layout.phtml" in="content"}}
 
    <? if($this->cart->items_count) {?>
    <p>Your cart contains {$#cart.items_count} items.</p>
 
    {{list using="$#cart.items" parity="$parity"}}
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
       <td>${$item.price|number:2, '.',' '}</td>
       <td>{$item.quantity}</td>
       <td>${$item.summ|number:2, '.', ' '}</td>
      </tr>
      {{/list:item}}
    </table>
    {{/list}}
 
    Total summ is : <b>${$#cart.total_summ|number:2, '.', ' '}</b>
    <br/>
    <a href="{{route_url params='action:empty'}}">Empty cart</a><br/>
    <a href="{{route_url params='action:checkout'}}">Checkout</a>
 
    <? } else { ?>
    You cart is empty at the moment!
    <? } ?>
    {{/wrap}}

В методе CartController :: doDisplay() мы передали объект корзины в шаблон. Это значит, что корзина доступна в корневом контейнере данных. Этим мы и воспользовались.

Подробнее о [передаче данных внутри MACRO-шаблонов](../../../../macro/docs/ru/macro/data_sources.md)

## Предварительные итоги
Попробуйте добавить несколько товаров при помощи панели управления и добавить их в корзину. Вы должны получить нечто следующее:

![Alt-cart](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:cart.png)

## Далее
Теперь мы можем приступить к оформлению заказав и сохранению их в базе данных. Этот пункт нашего плана покажет, как lmbActiveRecord поддерживает работу с различными отношениями между объектами, например, один-ко-многим или много-ко-многим.

Следующий шаг: [Шаг 7. Оформление и сохранение заказа в базе данных](./step7.md).
