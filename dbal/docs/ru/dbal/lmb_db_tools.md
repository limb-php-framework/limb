# lmbDbTools
**lmbDbTools** — инструментарий (tools) пакета DBAL. Автоматически добавляется в lmbToolkit при включении файла limb/dbal/common.inc.php

lmbDbTolls содержит следующий набор методов:

Метод | Назначение
------|-----------
**setDefaultDbDSN($dsn)** | Устанавливает параметры подключения к базе данных по-умолчанию. Это может быть строка вида «driver:user:password@host/database» или объект класса [lmb_db_dsn](./lmb_db_dsn.md).
**getDefaultDbDSN()** | Возвращает объект класса lmbDbDSN, который хранит параметры подключения к базе данных по-умолчанию.
**getDefaultDbConnection()** | Возвращает объект подключения к базе данных (connection), созданных на основе параметров по-умолчанию или же установленный явно при помощи метода setDefaultDbConnection($conn).
**setDefaultDbConnection($conn)** | Устанавливает новый объект подключения к базе данных, который будет использовать по-умолчанию
**createDbConnection($dsn)** | Создает объект подключения к базе данных на основе строки вида «driver:user:password@host/database» или объекта класса [lmb_db_dsn](./lmb_db_dsn.md).
**getDbInfo($conn)** | Возвращает объект meta-инфорации о базе данных, который поддерживает интерфейс lmbDbInfo. Meta-информация может кешироваться в файле если определена константа LIMB_VAR_DIR, а также определена константа **LIMB_CACHE_DB_META_IN_FILE** как true.
**cacheDbInfo($flag = true)** | Устанавливает явно флаг, нужно ли использовать закешированную meta-информацию.
**createTableGateway($table_name, $conn = null)** | Создает объект класса [lmb_table_gateway](./lmb_table_gateway.md).
