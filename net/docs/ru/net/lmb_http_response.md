# lmbHttpResponse
lmbHttpResponse — представляет ответ от системы к пользователю.

    $response = new lmbHttpResponse();
    $response->start();
    $response->header('HTTP/1.0 304 Not Modified');
    $response->write('Some content');
    $response->commit();

lmbHttpResponse работает по принципу транзакции:

* lmbHttpResponse :: start() — стартует транзакцию. После этого в lmbHttpResponse можно добавлять текст, заголовки, куки и т.д. Если транзакцию не начать - тогда при любом использовании lmbHttpResponse будет сгенерирован lmbException.
* lmbHttpResponse :: commit() — завершает транзакцию и оправляет данные lmbHttpResponse пользователю. Данные для lmbHttpResponse пользовалю сначала накапливаются, а при завершении транзакции отправляются все вместе.
* lmbHttpResponse :: reset() — до завершения транзакции можно очистить lmbHttpResponse.

см. также тест lmbHttpResponseTest.class.php
