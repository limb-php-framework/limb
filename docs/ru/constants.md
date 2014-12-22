# Использование переменных окружения для настройки системных параметров
В Limb3 выработавшейся практикой стало использование переменных окружения для настройки определенных системных параметров. Все переменные начинаются с ключевого слова LIMB_(единственное исключение - константы шаблонизатора WACT, которые имеют префикс WACT_). Для работы с переменными используются функции [lmb_env_set(), lmb_env_setor(), lmb_env_get(), lmb_env_has(), lmb_env_remove(), для отладки lmb_env_trace(), lmb_env_trace_has(), lmb_env_trace_show()](../../core/docs/ru/core/global_functions.md). Использование собственных функций, а так же наличие функций для отладки, избавляет от единственного их минуса - «беззащитности» перед плохим кодом.

Мы не отказываемся от настроечных файлов, однако считаем, что низкоуровневые параметры намного проще конфигурировать с помощью низкоуровневых средств. Для обслуживания переменных используется всего 100 строк кода, тогда как настроечные файлы тянут за собой определенные зависимости(например, класс, который осуществляет парсинг настроечных файлов). Сравните:

Используем переменную:

    require_once(lmb_env_get('LIMB_VAR_DIR') . '/foo.php');

Используем некую подсистему для получения параметров:

    require_once(Toolkit::instanse()->getConf('common')->get('var_dir') . '/foo.php');//вводим зависимость от пакетов toolkit и conf

## Поддержка механизма констант
Механизм констант, использованный ранее, поддерживается и сейчас. Константы просто транслируются в переменные. Например:

    define('LIMB_VAR_DIR', '/tmp/');
    echo lmb_env_get('LIMB_VAR_DIR'); //'/tmp/'

    lmb_env_set('LIMB_VAR_DIR', '/tmp/limb/');
    echo lmb_env_get('LIMB_VAR_DIR'); //'/tmp/limb/'

## «Сокращения» для часто используемых констант

Некоторые переменные очень часто используются внутри фреймворка. Чтобы не писать один и тот же скучный для них существует «обертка» в виде функций. Пока такая функция одна **lmb_var_dir()**:

    lmb_var_dir('/tmp'); //установка
    echo lmb_var_dir(); //получение значения

## Список наиболее часто используемых переменных
Документацию на не перечисленные здесь константы стоит искать в описании конкретного пакета.

**LIMB_APP_MODE** (string) — указывает на режим в котором функционирует приложение. Может принимать два значения: 'devel' и 'production'. Используется фильтром lmbErrorHandlerFilter из пакета web_app. В режиме 'devel' приложение выводит все сообщения об ошибках на экран. В режиме production только пишет их в лог.

**LIMB_CACHE_DB_META_IN_FILE** (boolean) — объявляет, стоит ли кешировать метаданные структуры базы данных. По умолчанию имеет значение false. Используется в пакете DBAL. Строить метаданные структуры БД при каждом запросе - довольно обременительная задача, поэтому имеет смысл эти данные кешировать(они хранятся в директории LIMB_VAR_DIR в файле db_info.xxx.cache). Отменять кеширование имеет смысл только во время разработки, но не на продукционных серверах. Пример использования:

    lmb_env_set('LIMB_CACHE_DB_META_IN_FILE', true);//включаем кеширование

**LIMB_CONF_INCLUDE_PATH** (string) — список путей через »;» указывающих директории относительно include_path, в которых стоит производить поиск *.conf.php и *.ini настроечных файлов. По умолчанию 'settings;limb/*/settings'. Используется в пакете CONFIG. Обычно полезно переопределять эту константу в тестах. Пример использования:

    lmb_env_set('LIMB_CONF_INCLUDE_PATH', 'tests/settings;settings');

**LIMB_CONTROLLERS_INCLUDE_PATH** (string) — список путей через »;» указывающих директории относительно include_path, в которых стоит производить поиск контроллеров. По умолчанию 'src/controller;limb/*/src/controller'. Используется в пакете WEB_APP.

**LIMB_SUPPORTED_VIEW_TYPES** (string) — список пар **<расширение файла шаблона>=<класс view>** через »;». Задает список поддерживаемых view-классов и их ассоциации с файлами-шаблонами. Например:

    lmb_env_set('LIMB_SUPPORTED_VIEW_TYPES', '.phtml=lmbMacroView;.html=lmbBlitzView;'); //поддерживаются macro-шаблоны с расширениями .phtml и blitz-шаблоны с расширениями .html

Поиск шаблонов происходит слева-направо, т.е. если, в примере, будет найден macro-шаблон, то [Blitz-шаблон](http://alexeyrybak.com/blitz/blitz_ru.html) искаться не будет.

**LIMB_VAR_DIR** (string) — указывает на некоторую доступную на запись директорию. Обычно в проекте эта директория имеет название **var** и находится в корневой директории проекта. Все временные данные, кеши, логи, т.е все то, что может быть безболезненно удалено хранится в этой директории. Эта директория должна всегда находиться в списке svn:ignore системы версионного контроля. Пример использования:

    lmb_env_set('LIMB_VAR_DIR', dirname(__FILE__) . '/var');
    lmb_var_dir(dirname(__FILE__) . '/var'); //"обертка" для быстрого доступа

**LIMB_UNDEFINED** (string) — Константа! Значение «отсутствие значения». Обычно используется для обозначения отсутствия значения, в случаях, когда null является значащей величиной. Например:

    /**
     *  Получение свойства по имени
     *  @param string property имя
     *  @param mixed значение, которое нужно вернуть, если свойство не установленно
     *  @return mixed
     */
    function get($property, $default = LIMB_UNDEFINED)
    {
      if(property_exists($this, $property))
        return $this->$property;

      if($default !== LIMB_UNDEFINED)
        return $default;
    }

LIMB_UNDEFINED уникально и неизменно на протяжении всего времени работы скрипта.

## Список остальных констант

* **LIMB_ENABLE_MOD_REWRITE** (bool) — использовать настройки для mod_rewrite
* **LIMB_FCKEDITOR_DIR** — директория FCK Editor. Используется в пакете WYSIWYG.
* **LIMB_HTTP_GATEWAY_PATH**
* **LIMB_HTTP_OFFSET_PATH** — путь limb-приложения относительно DOCUMENT_ROOT. Без слешей вначале и конце. Например, если index.php limb-приложения расположен DOCUMENT_ROOT/limb-app/index.php, то LIMB_HTTP_OFFSET_PATH должен быть равен 'limb-app'
* **LIMB_HTTP_SHARED_PATH** — путь [shared-ресурсов](../../web_app/docs/ru/web_app/app_installation.md) относительно DOCUMENT_ROOT
* **LIMB_JS_INCLUDE_PATH**
* **LIMB_LOCALE_INCLUDE_PATH**
* **LIMB_LOG_ENABLE**
* **LIMB_RULES_INCLUDE_PATH**
* **LIMB_TRANSLATIONS_INCLUDE_PATH**
* **LIMB_UTF**
* **LIMB_WEB_AGENT_REQUEST**
* **LIMB_WYSIWYG_DIR**

### Константы testman'а

* **LIMB_TEST_DB_DSN**
* **LIMB_TEST_RUNNER_CLASS_FORMAT**
* **LIMB_TESTS_RUNNER_CONFIG**
* **LIMB_TESTS_RUNNER_COVERAGE_REPORT_DIR**
* **LIMB_TESTS_RUNNER_FILE_FILTER**

## Список устаревших констант
Константы из этого списка не выставляются, но их значения поддерживаются, чтобы сохранить обратную совместимость.

* **PHPMAILER_VERSION_NAME**
* **LIMB_SMTP_AUTH**
* **LIMB_SMTP_HOST**
* **LIMB_SMTP_PASSWORD**
* **LIMB_SMTP_PORT**
* **LIMB_SMTP_USER**
* **LIMB_SMTP_SECURE**
* **LIMB_USE_PHPMAIL**
* **LIMB_MACRO_FILTERS_INCLUDE_PATH**
* **LIMB_MACRO_TAGS_INCLUDE_PATH**
