# Пакет CACHE2
Основное назначение пакета CACHE2 — обеспечение механизма кэширования. Кроме того [средства пакета позволяют](./cache2/misc.md):

* бороться с конкурентностью при генерировании кэша
* организовывать группы кэшей с помощью тэгов
* профилировать запросы к кэшу
* реализовать кэширование части macro-шаблона

## Абстракция от хранилища кэша

Для абстрагирования от конкретного хранилища кэша используется механизм соединений (семейство классов **lmbCache*Connection**). В данный момент имеются следующие соединения:

Идентификатор драйвера | Описание | Пример | Класс
-----------------------|----------|--------|-------
apc  | [APC](http://en.wikipedia.org/wiki/Alternative_PHP_Cache#Alternative_PHP_Cache_.28APC.29) | `apc:` | lmbCacheApcConnection
memcache | [Memcached](http://en.wikipedia.org/wiki/Memcached) | `memcache://<hostname>` | lmbCacheMemcacheConnection
memory | память php скрипта (работает в рамках одного скрипта) | `memory:` | lmbCacheMemoryConnection
db | БД (поддерживается mysql и mysqli) | `db://<db_dsn_name>?table=<table>` | lmbCacheDbConnection
file | файлы: один ключ — один файл | `file://<cache_dir>` | lmbCacheFileConnection
session | РНР сессии | `session:` | lmbCacheSessionConnection
fake | соединение «пустышка», для отладки | `fake:` | lmbCacheFakeConnection

### Интерфейс соединений
В общем случае кэш поддерживает следующие вызовы

    interface lmbCacheConnection
    { 
      function getType();
      function add ($key, $value, $ttl = false);
      function set ($key, $value, $ttl = false);
      function get ($key);  
      function delete($key);
      function flush();
      function increment ($key, $step = 1);
      function decrement ($key, $step = 1);
      function lock($key, $ttl = false, $lock_name = false);
      function unlock($key, $lock_name = false);
      function safeIncrement($key, $value = 1, $ttl = false)  
      function safeDecrement($key, $value = 1, $ttl = false)

## Настройка
Настройка производится с помощью файла cache.conf.php.

В скором времени соединения (в примере это default_cache_dsn и object_cache) скорее всего будут вынесены в отдельную секцию конфигурационного файла.

    <?php
    $conf['cache_enabled'] = false; //общий переключатель кэша (если кэш отключен, то используется %%lmbCacheFakeConnection%%)
    $conf['mint_cache_enabled'] = false; //защита от конкурентности при генерации кэша (lmbMintCache)
    $conf['cache_log_enabled'] = false; //логгирование операций (lmbLoggedCache)
    $conf['taggable_cache_enabled'] = false; //группировка ключей по тэгам (lmbTaggableCache)
 
    $conf['default_cache_dsn'] = "file:///tmp/cache2/"; //кэш по-умолчанию. В данном случае это будет lmbCacheFileConnection, т.е. хранение в файлах
    $conf['object_cache'] = "apc:"; //именованный кэш. В данном случае это будет lmbCacheApcConnection

## Пример работы
Пример работы с кэшом по-умолчанию:

    lmb_module_require('cache');
 
    $cache = lmbToolkit::instance()->getCache();
 
    $cache->set('key1', 1);
    var_dump($cache->get('key1');

Теперь воспользуемся именованным кэшом и сымитируем работу счетчика просмотров, в случае, если в кэше у нас хранятся объекты статей

$cache = lmbToolkit::instance()->getCache('objects_cache');
 
    $article_key = 'article'.$article_id;
    if($cache->lock($article_key))
    {
      $article = $cache->get($article_key);
      $article->views++;
      $cache->set($article_key, $article);
      $cache->unlock($article_key);
    }
