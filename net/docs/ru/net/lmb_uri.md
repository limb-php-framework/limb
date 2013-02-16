# lmbUri
Используется для разбора URL-ов и других связанных с ними операций.

Инициализация происходит через конструктор:

    $uri = new lmbUri('http://admin:test@localhost:81/test.php/test?foo=bar#23');

или parse метод:

    $uri = new lmbUri();
    $uri->parse('http://admin:test@localhost:81/test.php/test?foo=bar#23');

с полученным объектом можно проводить различные манипуляции, вот некоторые из них:

    $uri = new lmbUri('http://admin:test@localhost:81/test.php/test?foo=bar#23');
    $uri->setProtocol('ftp://');
    $uri->setPort('80');
    $string = $uri->toString(array('protocol', 'host', 'port', 'path'));

и т.д.

см. также тест lmbUriTest.class.php
