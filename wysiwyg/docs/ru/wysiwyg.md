# Пакет WYSIWYG
Пакет WYSIWYG предназначен для использования [Wysiwyg](http://en.wikipedia.org/wiki/Wysiwyg) редакторов внутри WACT-шаблонов. В данный момент поддерживаются редакторы [CKeditor](http://en.wikipedia.org/wiki/CKeditor).

Пакет добавляет в набор тегов WACT и MACRO, новый тег wysiwig. То, какой на самом деле будет вставлен редактор, зависит от настроек.

## Настройки
Настройки хранятся в файле wysiwyg.conf.php. В настройках задаются профили редакторов, которые указываются в wysiwyg теге.

Пример указания профиля в макро теге

    {{wysiwyg profile='ckeditor' [...]/}}

Пример простейшей настройки профилей

    <?php
    $conf = array(
      'default_profile' => 'ckeditor',

      'ckeditor' => array(
        'type' => 'ckeditor'
      ),
    );

Опция *default_profile* указывает на профиль по умолчанию.

Каждый профиль конфигурируется дополнительными опциями, часть которых используется виджетом (см. как это работает), а другая часть передаётся непосредственно редактору. Настройки пакета по умолчанию из *limb/wysiwyg/settings*:

    <?php

    $conf = array(

      'default_profile' => 'full',

      'full' => array(
        'type' => 'ckeditor',
        'basePath' => '/shared/wysiwyg/ckeditor/',
        'Config' => array(
          'toolbar' => 'Full',
          'uiColor' => '#FAFAFA',
          'customConfig' => '/shared/wysiwyg/ckeditor/config.js',
        ),
      ),

Как правило, разные профили используются для разных привилегий пользователя. Например, в административной зоне редактору нужны все средства редактора, а снаружи сайта, в поле для комментирования только ограниченный набор средств форматирования. Это нисколько не является обязательством, по этому разработчик может использовать другую удобную схему.

## Теги
### MACRO-тег {{wysiwyg}}
[Описание тега {{wysiwyg}}](../../../macro/docs/ru/macro/tags/wysiwyg_tags/lmb_wysiwyg_tag.md)

## Настройки редакторов
### Настройка CKEditor-а
Секция [CKEditor] настроечного файла описывает, какой компонент будет реализовывать отображения редактора, а также задает наиболее важные настройки:

* base_path — web-путь, где лежит ckeditor.
* Config[CustomConfigurationsPath] — указывает на web-путь до файла, который определяет дополнительные настроки ckeditor-а.
* Config[customConfig] — указывает на web-путь до файла, который определяет дополнительные настроки ckeditor-а.

CKEditor поставляется вместе с пакетом WYSIWYG и лежит в папке /shared/ckeditor. Обычно при разработке мы создаем или алиас на эту папку или же сим-линк:

    <VirtualHost 127.0.0.1>
        DocumentRoot /var/dev/project/www
        ServerName project.my_comp.bit
        Alias /shared/wysiwyg        /var/dev/limb/wysiwyg/shared
    </VirtualHost>

Файл, который указан опцией Config[customConfig] обычно содержит описание того, какие наборы инструментов будет содержат редактор и какие скрипты будут отвечать за загрузку и отображение файлов и изображений, например:

	project_browse_path = '/lib/kcfinder/browse.php';
	project_upload_path = '/lib/kcfinder/upload.php';

    config.filebrowserUploadUrl = project_upload_path + '?type=files';
    config.filebrowserImageUploadUrl = project_upload_path + '?type=images';
    config.filebrowserFlashUploadUrl = project_upload_path + '?type=flash';
    ...

Подробная информация о настройке CKEditor'a находится на сайте проекта.

По-умолчанию используется KCFinder браузер и аплоадер файлов, который лежит в папке limb/wysiwyg/shared/kcfinder/.

## Как работает пакет
Каждому редактору соответствует класс, отнаследованный от *lmbMacroBaseWysiwygWidget* (в случае использования устаревшего WACT используется другой базовый класс), в котором метод *renderWysiwyg()* «рисует» редактор. Все виджеты зарегистрированы в *lmbWysiwygConfigurationHelper*.

При обработке в шаблоне тега [{{wysiwyg}}](../../../macro/docs/ru/macro/tags/wysiwyg_tags/lmb_wysiwyg_tag.md) с помощью *lmbWysiwygConfigurationHelper* подключается тот или иной виджет, отвечающий за указанный в атрибуте профиль и таким образом отрисовывается необходимый редактор с соответствующими настройками.
