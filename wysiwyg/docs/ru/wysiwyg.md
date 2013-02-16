# Пакет WYSIWYG
Пакет WYSIWYG предназначен для использования [Wysiwyg](http://en.wikipedia.org/wiki/Wysiwyg) редакторов внутри WACT-шаблонов. В данный момент поддерживаются редакторы [CKeditor](http://en.wikipedia.org/wiki/CKeditor), старый [FCKeditor](http://en.wikipedia.org/wiki/FCKeditor) и [TinyMCE](http://en.wikipedia.org/wiki/TinyMCE).

Пакет добавляет в набор тегов WACT и MACRO, новый тег wysiwig. То, какой на самом деле будет вставлен редактор, зависит от настроек.

## Настройки
Настройки хранятся в файле wysiwyg.conf.php. В настройках задаются профили редакторов, которые указываются в wysiwyg теге.

Пример указания профиля в макро теге

    {{wysiwyg profile='tinymce' [...]/}}

Пример простейшей настройки профилей

    <?php
    $conf = array(
      'default_profile' => 'ckeditor',
 
      'ckeditor' => array(
        'type' => 'ckeditor'
      ),
 
      'tinymce' => array(
        'type' => 'tinymce'
      )  
    );

Опция *default_profile* указывает на профиль по умолчанию.

Каждый профиль конфигурируется дополнительными опциями, часть которых используется виджетом (см. как это работает), а другая часть передаётся непосредственно редактору. Настройки пакета по умолчанию из *limb/wysiwyg/settings*:

    <?php
 
    $conf = array(
 
      'default_profile' => 'default_ckeditor',
 
      ''default_ckeditor' => array(
        'type' => 'ckeditor',
        'basePath' => '/shared/wysiwyg/ckeditor/',
        'Config' => array(
          'toolbar' => 'Full',
          'uiColor' => '#9AB8F3',
          'customConfig' => '/shared/wysiwyg/ckeditor/ckeditor_config.js',
        'filebrowserBrowseUrl' => '/shared/wysiwyg/kcfinder/browse.php?type=files&opener=ckeditor',
    	  'filebrowserImageBrowseUrl' => '/shared/wysiwyg/kcfinder/browse.php?type=images&opener=ckeditor',
          'filebrowserFlashBrowseUrl' => '/shared/wysiwyg/kcfinder/browse.php?type=flash&opener=ckeditor',
          'filebrowserUploadUrl' => '/shared/wysiwyg/kcfinder/upload.php?type=files&opener=ckeditor',
          'filebrowserImageUploadUrl' => '/shared/wysiwyg/kcfinder/upload.php?type=images&opener=ckeditor',
          'filebrowserFlashUploadUrl' => '/shared/wysiwyg/kcfinder/upload.php?type=flash&opener=ckeditor'
        ),
      ),
 
      'default_fckeditor' => array(
        'type' => 'fckeditor',
        'width' => '600px',
        'height' => '400px',
        'cols' => '100',
        'rows' => '15',
        'Config' => array('CustomConfigurationsPath' => '/shared/wysiwyg/fckeditor/fckconfig.js'),
        'ToolbarSet' => 'Default'
      ),
 
      'default_tinymce' => array(
        'type' => 'tinymce',
        'width' => '600px',
        'height' => '400px',
        'cols' => 100,
        'rows' => 15,
        'base_path' => '/shared/wysiwyg/tiny_mce/',
        'editor' => array(
          'language' => 'en',
          'mode' => "textareas",
          'theme' => "advanced",
      )
    );

Как правило, разные профили используются для разных привилегий пользователя. Например, в административной зоне редактору нужны все средства редактора, а снаружи сайта, в поле для комментирования только ограниченный набор средств форматирования. Это нисколько не является обязательством, по этому разработчик может использовать другую удобную схему.

## Теги
### MACRO-тег {{wysiwyg}}
[Описание тега {{wysiwyg}}](../../../macro/docs/ru/macro/tags/wysiwyg_tags/lmb_wysiwyg_tag.md)

## Настройки редакторов
### Настройка CKEditor-а
### Настройка FCKEditor-а
Секция [FCKEditor] настроечного файла описывает, какой компонент будет реализовывать отображения редактора, а также задает наиболее важные настройки:

* base_path — web-путь, где лежит fckeditor.
* dir — абсолютный путь до места, где лежит fckeditor.
* Config[CustomConfigurationsPath] — указывает на web-путь до файла, который определяет дополнительные настроки fckeditor-а.

FCKEditor поставляется вместе с пакетом WYSIWYG и лежит в папке /shared/fckeditor. Обычно при разработке мы создаем или алиас на эту папку или же сим-линк:

    <VirtualHost 127.0.0.1>
        DocumentRoot /var/dev/project/www
        ServerName project.my_comp.bit
        Alias /shared/js        /var/dev/limb/3.x/packages/js/shared
        Alias /shared/wysiwyg        /var/dev/limb/3.x/packages/wysiwyg/shared
    </VirtualHost>

Файл, который указан опцией Config[CustomConfigurationsPath] обычно содержит описание того, какие наборы инструментов будет содержат редактор и какие скрипты будут отвечать за загрузку и отображение файлов и изображений, например:

    var _FileBrowserLanguage  = 'php' ;	// asp | aspx | cfm | lasso | perl | php | py
    var _FileBrowserExtension = _FileBrowserLanguage == 'perl' ? 'cgi' : _FileBrowserLanguage ;
 
    FCKConfig.LinkBrowser = true ;
    FCKConfig.LinkBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/browser.html?Connector=connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
    FCKConfig.LinkBrowserWindowWidth	= FCKConfig.ScreenWidth * 0.7 ;		// 70%
    FCKConfig.LinkBrowserWindowHeight	= FCKConfig.ScreenHeight * 0.7 ;	// 70%
    ...

Подробная информация о настройке FCKEditor'a находится на сайте проекта.

По-умолчанию используется встроенный в FCKEditor браузер и аплоадер файлов, который лежит в папке limb/wysiwyg/shared/fckeditor/editor/filemanager/.

### Настройка TinyMCE
## Как работает пакет
Каждому редактору соответствует класс, отнаследованный от *lmbMacroBaseWysiwygWidget* (в случае использования устаревшего WACT используется другой базовый класс), в котором метод *renderWysiwyg()* «рисует» редактор. Все виджеты зарегистрированы в *lmbWysiwygConfigurationHelper*.

При обработке в шаблоне тега [{{wysiwyg}}](../../../macro/docs/ru/macro/tags/wysiwyg_tags/lmb_wysiwyg_tag.md) с помощью *lmbWysiwygConfigurationHelper* подключается тот или иной виджет, отвечающий за указанный в атрибуте профиль и таким образом отрисовывается необходимый редактор с соответствующими настройками.
