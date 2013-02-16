# Класс lmbRoutes
Идея класса lmbRoutes заключается в том, чтобы получать массив именованных параметров из строки посредством применения определенных паттернов (route) к данной строке и наоборот — из массива параметрв формировать строку. В качестве строки обычно используется URL, таким образом lmbRoutes обычно используется в [разборе Запроса](./request_dispatching.md) для получения параметров.

## lmbRoutes для разбора запроса
Список паттернов lmbRoutes получает в конструктор:

    $config = array(array('path' => 'blog/',
                          'defaults' => array('controller' => 'blog',
                                              'action' => 'display')),
                    array('path' => 'blog/:year/:month/:day:',
                          'defaults' => array('controller' => 'blog',
                                              'action' => 'archive',
                                              'year' => date('Y'),
                                              'month' => $default_month = date('m'),
                                              'day' => $default_day = date('d')),
                          'requirements' => array('year' => '/(19|20)\d\d/',
                                                  'month' => '/[01]?\d/',
                                                  'day' => '/[0-3]?\d/')),
                    array('path' => 'blog/:action:.html',
                          'defaults' => array('service' => 'Blog',
                                              'action' => 'display')));
 
    $routes = new lmbRoutes($config);

Список паттернов (конфиг) — это обычный массив, каждый элемент которого — один **паттерн**. Паттерн состоит из секций:

* **path**, который указывает сам паттерн,
* **defaults**, где указываются значения по-умолчанию параметров, которые можно получить из паттерна,
* **requirements**, которые указывают на регулярные значения, которые должны выполняться для некоторых параметров, чтобы паттерн мог считаться подходящим для строки, пришедшей в метод dispatch($url).

То есть, при использовании указанного конфига, на вызов:

    $result = $routes->dispatch('/blog/edit.html');

сработает последний роут из списка, и в $result попадут следующие параметры:

    array('controller' => 'blog',
          'action' => 'edit')

И более сложный пример:

    $result = $routes->dispatch('/blog/2004/12');

будет означать получение следующих параметров в $result:

    array('controller' => 'blog',
          'action' => 'archive',
          'year' => 2004,
          'month' => 12,
          'day' => XX ); // текущий день, так как будет использовано default значение параметра.

## lmbRoutes доступен через lmbToolkit
По-умолчанию объект класса lmbRoutes доступен через lmbToolkit при помощи метода getRoutes(), который добавляется в lmbToolkit через [lmbWebAppTools](./lmb_web_app_tools.md):

    $routes = lmbToolkit :: instance()->getRoutes();
    $params = $routes->dispatch('/news/archive');

Объект класса lmbRoutes, получаемый через lmbToolkit, конфигурируются при помощи содержимого файла **routes.conf.php**, который обычно хранится в папке /settings. Вот пример содержимого такого файла:

    <?php
    $conf = array(array('path' => '/:controller/:action',
                        'defaults' => array('controller' => 'news',
                                            'action' => 'display')),
                 array('path' => '/:controller/:action/:id',
                        'defaults' => array('action' => 'display')));
    ?>

## lmbRoutes для составления URL-ов
Итак, lmbRoutes :: **dispatch($url)** используется для получения массива параметров по строке.

Существует также обратная операция: lmbRoutes :: **toUrl($params, $route = ' ')**.

Второй параметр метода toUrl — это название паттерна. То есть паттерны могуть быть именованными, например:

     $config = array('default' => array('path' => '/:controller/display',
                           'defaults' => array('action' => 'display')),
                      'full' => array('path' => '/:controller/:action',
                           'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config); 

Если имя паттерна **$route** не указано, lmbRoutes пытается угадать, какой паттерн больше всего подходит. Если ни один паттерн нельзя применить, тогда генерится исключительная ситуация (exception).

Для вызова метода toUrl объекта класса lmbRoutes, который доступен через lmbToolkit, существует алиас:

    $url = lmbToolkit :: instance()->getRoutesUrl($params, $route_name = '', $skip_controller = false));

Этот алиас используется в тегах `<route_url>` и `<route_url_set>`, при помощи которых можно составлять url-ы из параметров прямо в шаблоне.

Параметр $route_name позволяет указать имя маршрута явно. **$skip_controller** нужен для того, чтобы отменить добавление имени текущего контроллера в список параметров, что делает тулкит по-умолчанию. Это важно, если например, маршрут не содержит такого параметра controller или содержит его в списке defaults.

Детали работы класса lmbRoutes см. в тестах lmbRoutesDispatchTest.class.php и lmbRoutesToUrlTest.class.php
