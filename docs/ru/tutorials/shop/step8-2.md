# Шаг 8.2. Отправка заказа
## Класс CartController оформление заказа
Приведем код изменений в классе, а потом поясним все моменты.

Файл shop/src/controller/CartController.class.php:

    <?php
    lmb_require('src/model/Product.class.php');
    lmb_require('src/model/Cart.class.php');
 
    class CartController extends lmbController
    {
      protected function _checkoutCart($cart)
      {
        $user = lmbToolkit::instance()->getUser();
        $this->view->set('cart', $cart);
        $this->view->set('address', $user->getAdress());
        $this->useForm('checkout_form');    
 
        if(!$this->request->hasPost())
        {
          if(!$cart->getItemsCount())
            return $this->flashAndRedirect('Your cart is empty! Nothing to checkout!', array('controller' => 'main_page'));
 
          if(!$user->getIsLoggedIn())
            return $this->flashAndRedirect('Your are not logged in yet! Please login or register to checkout!');
        }
        else
        {      
          $order = Order :: createForCart($cart);
          $order->setAddress($this->request->get('address'));
          $order->setUser($user);
 
          if($order->trySave($this->error_list))
          {
            $cart->reset();
            return $this->flashAndRedirect('Your order has been sent. Your cart is now empty.', array('controller' => 'main_page'));
          }            
        }
      }
 
      function doDisplay()
      {
        $this->view->set('cart', $this->_getCart());
      }
 
      function doCheckout()
      {
        $cart = $this->_getCart();
        return $this->_checkoutCart($cart);
      }
    [...]

Итак, начнем наши пояснения.

    [...]
    class CartController extends lmbController
    {
      protected function _checkoutCart($cart)
      {
    [...]

Для оформления заказа мы создали отдельный protected метод _checkoutCart($cart) в который будем передавать объект корзины $cart.

Передача ошибок валидации в шаблон производится автоматически.

    class CartController extends lmbController
    {
      [...]
      protected function _checkoutCart($cart)
      {
        [...]
        if(!$this->request->hasPost())
        {
          if(!$cart->getItemsCount())
            return $this->flashAndRedirect('Your cart is empty! Nothing to checkout!', array('controller' => 'main_page'));
 
          if(!$user->getIsLoggedIn())
            return $this->flashAndRedirect('Your are not logged in yet! Please login or register to checkout!');
        }
        [...]
      }
      [...]
    }

Используя объект **request** в условии **if(!$this→request→hasPost()))**, определяем что был запрос на отправку заказа и далее делаем 2 проверки:

* корзина должна содержать хотя бы одну товарную позицию.
* пользователь должен быть залогинен.

Если какое-либо из этих условий не выполняется, мы даем знать пользователю об этом при помощи метода flashAndRedirect() и перебрасываем его на главную страницу сайта.

    class CartController extends lmbController
    {
      [...]
      protected function _checkoutCart($cart)
      {
        $user = lmbToolkit::instance()->getUser();
        $this->view->set('cart', $cart);
        $this->view->set('address', $user->getAdress());
        $this->useForm('checkout_form');    
      [...]
      }

Если форма будет отображена в первый раз (не вообще в первый раз, имеется ввиду отображение начального состояния формы, а не тогда когда она была отправлена), мы должны заполнить ее содержимое данными текущего пользователя.

Плюс мы передаем в шаблон объект корзины для того, чтобы иметь возможность вывести содержимое корзины на страницы.

И последнее…

    class CartController extends lmbController
    {
      [...]
      protected function _checkoutCart($cart)
         [...]
          $order = Order :: createForCart($cart);
          $order->setAddress($this->request->get('address'));
          $order->setUser($user);
 
          if($order->trySave($this->error_list))
          {
            $cart->reset();
            return $this->flashAndRedirect('Your order has been sent. Your cart is now empty.', array('controller' => 'main_page'));
          }            
        }
      }
      [...]
    }

Мы создали новый объект класса Order при помощи статического метода createForCart($cart), заполнили поле адреса доступки address и указали пользователя. Если заказ был успешно сохранен, мы ошищаем корзину при помощи метода **reset()**, даем знать пользователю, что операция прошла успешно и перебрасываем его на главную страницу.

Теперь нам необходимо вызвать только что созданный protected метод

Файл shop/src/controller/CartController.class.php:

    <?php
    class CartController extends lmbController
    {
      [...]
      function doCheckout()
      {
        $cart = $this->_getCart();
        return $this->_checkoutCart($cart);
      }
      [...]
    }
    ?>

## Шаблон /cart/checkout.phtml
Наконец, приведем код шаблона по оформлению заказа cart/checkout.phtml

Файл shop/template/cart/checkout.phtml:

    <? $this->title ='Checkout'; ?>
 
    {{wrap with="front_page_layout.phtml" in="content"}}
 
    Your cart contains {$#cart.items_count} items.
 
    {{list using="$#cart.items"}}
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
      <tr class='checkout'>
       <td>{$item.product.title}</td>
       <td>${$item.price|number:2, '.', ' '}</td>
       <td>{$item.quantity}</td>
       <td>${$item.summ|number:2, '.', ' '}</td>
      </tr>
      {{/list:item}}
    </table>
    {{/list}}
 
    Total summ is : <b>${$#cart.total_summ|number:2, '.', ' '}</b>
 
    <br/>
    {{form name='checkout_form' id='checkout_form' method='POST'}}
 
      <label for='address'>Delivery address:</label><br/>
      {{textarea type="text" name="address" id="address" title="Delivery address"/}}<br/>
 
      <input type='submit' class='button' name='submitted' value="Finish order" class='button'/><br/>
    {{/form}}
    {{/wrap}}

Шаблон содержит отображение списка товарных позиций корзины, также как и в шаблоне /cart/display.phtml.

Ниже находится форма, которая позволяет ввести адрес доставки. Помните, что мы передали в эту форму данные текущего пользователя, поэтому если пользователь ранее ввел адрес доставки при регистрации или на странице профайла, то это значение будет отображено в соответствующем поле.

## Предварительные итоги
Ссылка на действие по оформлению заказа уже должны была у нас быть на странице /cart.

Вот так должна выглядеть страница /cart/checkout, если мы предварительно положим что-нибудь в корзину:

![Alt-checkout](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:checkout.png)

## Далее
Наши пользователи могут теперь добавлять новые заказы. Осталость совсем немного из того, что мы запланировали:

* работа с заказами в панели управления,
* просмотр покупателями своих заказов.

Итак, следующий шаг: [Шаг 9. Работа с заказами](./step9.md).
