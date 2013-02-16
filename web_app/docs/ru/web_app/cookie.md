# Работа с cookies
Для считывания/записи cookies в Limb3 существуют следующие средства:

* В классе [lmbHttpRequest](../../../../net/docs/ru/net/lmb_http_request.md) есть метод: **lmbHttpRequest :: getCookie($cookie)**.
* В классе [lmbHttpResponse](../../../../net/docs/ru/net/lmb_http_response.md) есть метод: **lmbHttpResponse :: setCookie($name, $value, $expire = 0, $path = '/', $domain = ' ', $secure = false)**.

Объект $request класса lmbHttpRequest можно получить из тулкита при помощи метода getRequest(). Объект $response класса lmbHttpResponse можно получить из тулкита при помощи метода getResponse():

    $request = lmbToolkit :: instance()->getRequest();
    $response = lmbToolkit :: instance()->getResponse();

Например, код, который сохраняет выбранные элементы каталога, которые пользователь положил в корзину:

    function saveCart($cart)
    {
      $item_ids = array();
      foreach($cart->getItems() as $item)
        $item_ids[] = $item->getId();
 
      $response = lmbToolkit :: instance()->getResponse();
      $response->setCookie('CartItems', implode(',', $item_ids));
    }
