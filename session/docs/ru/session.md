# Пакет SESSION — средства для прозрачной работы с сессиями
## Основные классы
* **lmbSessionStorage** — интерфейс для хранителей данных сессии.
* **lmbSessionDbStorage** — хранитель сессии в базе данных.
* **lmbSession** — класс для работы с сессионными данными. Классы приложений, построенных на базе Limb не должны работать с глобальной переменной $_SESSION, только через класс lmbSession.
* **lmbSessionNativeStorage** — базовый драйвер хранилища сессионных данных на основе PHP-файлов. По сути, это простая заглушка.
* **lmbSessionDBStorage** — драйвер хранилища сессионных данных на основе таблицы базы данных.

## Инициализация сессии
Порядок работы с пакетом SESSION таков:

1. Создается объект lmbSession.
2. Создается объект, реализующий интерфейс lmbSessionStorage, например, lmbSessionDbStorage.
3. Вызывается инстанс-метод lmbSession :: start($storage = null), куда передается драйвер хранилища сессионных данных. Если драйвер не передается - используется обычный для PHP способ хранения сессионных данных в файлах.
4. Далее объект сессии используется глобально или каким-либо еще способом передается клиентам.

## Где используется пакет SESSION
Пакет SESSION используется пакетом WEB_APP. см. раздел [Работа с сессий в пакете WEB_APP](../../../web_app/docs/ru/web_app/session.md).

## Класс lmbSessionDbStorage
lmbSessionDbStorage — класс, который позволяет хранить сессионные данные в базе данных. Так как lmbSessionDbStorage работает с базой данных, при использовании этого драйвера сессии, появляется зависимость от [пакета DBAL](../../../dbal/docs/ru/dbal.md).

SQL-код структуры таблицы, где хранятся сессионные данные показан ниже:

    CREATE TABLE `sys_session` (                              
      `session_id` VARCHAR(50) NOT NULL DEFAULT '',           
      `session_data` BLOB NOT NULL,                           
      `last_activity_time` BIGINT(11) UNSIGNED DEFAULT NULL,  
      PRIMARY KEY  (`session_id`)                            
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 

lmbSessionDbStorage в методе installStorage() регистрирует в качестве обработчиков сессионных сообщений свои методы, такие как sessionStart(), sessionRead(), sessionWrite() и т.д.

## Класс lmbSessionNativeStorage
lmbSessionNativeStorage — класс, который используется, если вы желаете использовать стандартную функциональность PHP по хранению сессионных данных в файлах. По сути lmbSessionNativeStorage - это заглушка.
