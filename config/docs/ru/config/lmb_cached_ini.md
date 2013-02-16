# lmbCachedIni
lmbCachedIni — дочерний класс от [lmbIni](./lmb_ini.md), позволяет кешировать результаты парсинга ini-файлов. Хранит кеш в папке VAR_DIR/ini/. Кеширование может быть отключено посредство определения константы INI_CACHE_ENABLED (по-умолчанию, кеширование включено):

    define('INI_CACHE_ENABLED', false);
