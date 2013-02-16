# lmbFileLocator
**Класс lmbFileLocator** — основной класс пакета FS, который ищет файлы по алиасам при помощи «списка локаций». Список локаций - это объект, реализующего интерфейс [lmb_file_locations](./lmb_file_locations.md).

**Алиас** — это укороченное название файла, например news/index.html. Полный путь до файла (резолвинг алиаса) будет определен объектом класса lmbFileLocator. В результате резолвинга алиса мы можем получить что-то наподобие /var/dev/project/my_project/templates/ru/news/index.html.

Общая схема реализации этой подсистемы выглядит следующим образом:

![Alt-lmbFileLocator](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:fs:limb3_file_schema.png)

Итак, lmbFileLocator принимает объект, реализующий lmbFileLocations:

    $locator = new lmbFileLocator(new lmbFileLocationsList(array('/dir1', '/dir2', '/dir3')));

Для поиска файлов lmbFileLocator содержит два метода:

* **locate($alias, $params = array())**
* **locateAll($prefix = '')**.

* $alias — это укороченное название файла, который нужно найти.
* $params — список дополнительных параметров для поиска, передаются в список локаций.

Например:

    $locator->locate('news/news.html', array('lan' => 'ru'));

[lmb_file_locator](./lmb_file_locator.md) часто используется в наборах инструметов (tools), например, в пакете [WEB_APP](../../../../web_app/docs/ru/web_app.md) в классе [lmbWebAppTools](../../../../web_app/docs/ru/web_app/lmb_web_app_tools.md) :: createController($controller_name) или в пакете VIEW в методе lmbViewTools :: getWactLocator().

Пример создания локатора:

    $locations = new lmbIncludePathFileLocations('/settings/');
    $locator = new lmbCachingFileLocator(new lmbFileLocator($locations));

Здесь будет создан кеширующий [lmbCachingFileLocator](./lmb_caching_file_locator.md), который будет искать любые файлы файлы в папках /setting/ в папках, указанных в include_path. Например, при поиске conf-файла routes.conf.php он может вернуть var/dev/limb/web_app/settings/routes.conf.php

Пример инициализации локатора можно также увидеть в методе lmbFsTools :: getFileLocator().
