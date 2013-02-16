# lmbWebAppTools
**lmbWebAppTools** — базовый набор инструментов пакета WEB_APP. Добавляется в тулкит автоматически при включении файла limb/web_app/common.inc.php

lmbWebAppTools содержит методы для получения самых базовых для web-приложения объектов, например:

* **getRequest()** — возвращает объект Запроса к системе
* **getResponse()** — возвращает объект Ответа системы
* **getSession()** — возвращает объект Сессии (см. [работа с сессией](./session.md))
* **getView()** — возвращает текущий [View](./view.md)
* **getDispatchedController()** — возвращает dispatched controller, то есть текущий контроллер, определенный в результате [разбора Запроса](./request_dispatching.md)
* **getFlashBox()** — возвращает объект flash_box, который хранит сообщения приложения к пользователю (см. [использование flash box](./flash_box.md))
* **getRoutes()** — возвращает объект класса [lmbRoutes](./lmb_routes.md), который используется в [разборе запроса](./request_dispatching.md) и при составлении url-ов на основе так называемых маршрутов.

Кроме getter-ов, **есть соответствующие setter-ы**, то есть setDispatchedRequest(), setView() и т.д.

Также есть набор служебных методов:

* **createController($controller_name)** — получает имя контроллера в under_scores и создает объект контроллера. Например, news → new NewsContoller(). Файл с классом контроллера ищется на основе путей, указанных константой LIMB_CONTROLLERS_INCLUDE_PATH (см. [Использование констант для настройки системных параметров](../../../../docs/ru/constants.md))
* **getRoutesUrl($params = array(), $route_name = ' ', $skip_controller = false)** — по сути, алиас для метода [lmbRoutes](./lmb_routes.md) :: **toUrl()**, который позволяет сформировать url на основе параметров и маршрута. Обратим внимание, что парамет controller с именем текущего контроллера автоматически добавляется в $params. Параметр $skip_controller используется, чтобы отменить это поведение.
* **redirect($params_or_url = array(), $route_url = null, $append = '')** — фактически — алиас для метода lmbHTTPResponse :: redirect($url) с тем отличием, что если в качестве первого аргумента указан массив — автоматически вызывает метод getRoutesUrl() для формирования пути по марштруту, например:

    lmbToolkit :: instance()->redirect(array('action' => 'archive', 'id' => $news_id), 'news_archive_route');
    lmbToolkit :: instance()->redirect('/news/archive/' . $news_id);

Параметр $append позволяет добавить к url-у какое-либо окончание:

    lmbToolkit :: instance()->redirect(array('action' => 'archive', 'id' => $news_id), 'news_archive_route', '?is_approved = true');
    // получим редикт на страницу с например таким адресом /news/archive/102?is_approved=true

* **flashError($message)** — добавляет ошибку во flash_box
* **flashMessage($message)** — добавляет обычно сообщение во flash_box
