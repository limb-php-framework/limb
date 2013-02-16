# lmbRoutesRequestDispatcher
lmbRoutesRequestDispatcher — разборщик Запроса, реализует интерфейс [lmbRequestDispatcher](./lmb_request_dispatcher.md). lmbRoutesRequestDispatcher опирается на работу класса lmbRoutes, объект которого разборщик берет из тулкина (метод getRoutes() доступен через lmbWebAppTools).

По-сути lmbRoutesRequestDispatcher лишь вызывает [lmbRoutes](./lmb_routes.md) и передает ему путь из lmbUri. Если lmbRoutes не возвращает параметра «action» из своего метода dispatch($url), когда lmbRoutesRequestDispatcher попытается использовать параметр action из Запроса. Полученный результат (массив параметров) возвращается.
