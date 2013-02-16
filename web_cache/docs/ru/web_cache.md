# web_cache.md
## Пакет WEB_CACHE для задач web кеширования
Идея web кеширования очень проста: получить динамичный контент, сохранить его в статичный файл и выдавать этот статичный файл при каждом запросе.

Пакет включается в LIMB приложение как фильтр lmbFullPageCacheFilter следующим способом

    class LimbApplication extends lmbWebApplication
    {
      function __construct()
      {
      $this->registerFilter(new lmbHandle('limb/web_cache/src/filter/lmbFullPageCacheFilter.class.php'));
    	parent::__construct();
      }
    }

При первом запросе, которому соответствует кеширующее правило, пакет сохраняет то что выводится в браузер и в следующий раз напрямую выдаст эти данные, причём остальные фильтры в приложении не сработают. Благодаря этому скорость работы приложения заметно повышается.

Однако надо иметь ввиду, что вместе с системой кеша возникает проблема его обновления. Пакет WEB_CACHE имеет несколько методов, которые удобно использовать при изменении данных в административной панели. Например где то в контроллере можно написать что-то вроде

    function _onAfterSave()
     {
       $cache_writer = new lmbFullPageCacheWriter( lmb_env_get('LIMB_VAR_DIR') . '/fpcache/');
       $cache_writer->flushAll();
     }

## Настройки правил кеширования
Для настроек путей по умолчанию используется full_page_cache.ini, который имеет следующие настройки

### path_regex
Регулярное выражение запроса. Для проверки подставляется путь из запроса. То есть при таком запросе http://example.com/some/path?arg=1 будет проверяться строка */some/path*

Пример правила для кеширования главной страницы

    [main_page]
    path_regex = ~^/$~
    type = allow

### type

Указывает кешировать *allow* или нет deny данный запрос

Пример правила для запрета кеширования

    [main_page]
    path_regex = ~^/document/xml$~
    type = deny

### request,get,post
С помощью этих опций задаются параметры запроса для соответствия правилу. Значение «*» означает как минимум один параметр в запросе, значение »!» означает ни одного параметра.

Данное правило означает, что кеширование сработает для пути */document* с любым GET, POST и COOKIE(?) параметром. Однако оно не сработает без параметров в запросе

    [main_page]
    path_regex = ~^/document$~
    type = allow
    request = *

Такое правило будет кешировать страницу, только если нет POST запроса

    [main_page]
    path_regex = ~^/contacts$~
    type = allow
    post = !

Также можно указать названия параметров

    [main_page]
    path_regex = ~^/search$~
    type = deny
    get[q] = *

Для совсем точного соответствия можно указать ещё и значения параметров

    [main_page]
    path_regex = ~^/page$~
    type = allow
    get[foo] = 1
    get[bar] = 2

### path_offset

Смещение в пути, начиная с которого проверяется регулярное выражение из *path_regex*

Правило сработает для http://example.com/document/map

    [sitemap]
    path_offset = /document
    path_regex = ~^/map~
    type = allow
