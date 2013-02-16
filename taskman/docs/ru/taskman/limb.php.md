# Утилита limb.php
Утилита limb.php предназначена для поиска и исполнения taskman-задач. Файл limb.php находится в корне фреймворка.

## Установка
### Windows
В корне фреймворка находится файл limb.bat, который необходимо скопировать в одну из папок переменной окружения PATH. При необходимости, в нем нужно изменить значение переменной PHP_COMMAND, на путь до файла php.exe.

### Unix
Предпочтительный способ — использование символической ссылки:

    $ sudo ln -s /<limb_path>/limb /usr/bin/limb
    $ sudo chmod 755 /usr/bin/limb

## Использование
Пример работы limb.php:

    $ limb project
    Project directory [/www/limb]: /www/new-limb-app
    Limb directory [.]: 
    ...

В первых двух строчках limb.php пытается определить директории проекта и limb.

Если вас утомляет ввод этих директорий при каждом запуске скрипта, то вы можете задать их в опциях вызова PHP:

    $ php -d limb.dir=/www/limb -d limb.project_dir=/www/new-limb-app /www/limb/limb.php

или дописать их в **php.ini**

    limb.dir = /www/limb 
    limb.project_dir = /www/new-limb-app

### Запуск задания
Утилита limb.php, сканирует все пакеты на наличие заданий, по этому их запуск не требует указания пути к файлу, где они находятся. Достаточно указать только название задания. Такой вызов в командной строке запустит задание entity_create из пакета *constructor*

    $php limb.php entity_create

## Список тасков
[taskman](./taskman.md)

## Пакет CONSTRUCTOR
[constructor](../../../../constructor/docs/ru/constructor.md)

### Пакет MIGRATE
Пакет находится в инкубаторе. Для использования его функционала необходимо перенести содержимое папки migrate в папку с пакетами limb.

#### Задачи пакета Migrate
Тег | Описание | Зависимости
----|----------|------------
| |Расположение: limb/migrate/cli/migrate.inc.php
migrate_init_config	| Init project config. Creates migrate.conf.php in project/settings dir. By default: **DSN**: used project db.conf.php **Schema**: project_dir/init/migrate/schema.sql **Data**: project_dir/init/migrate/data.sql **Migrations**: project_dir/init/migrate/migrations/	
migrate_init | Init project migration: create dump files and sets version 1	| migrate_init_config
migrate_dump_all | Dump schema and data	| migrate_init_config
migrate_dump_schema	| Dump schema | migrate_init_config
migrate_dump_data	| Dump data | migrate_init_config
migrate_load_all | Load schema and data	| migrate_init_config
migrate_create | Create new migration file (using diff)	| migrate_init_config
migrate_run	| Apply new migration file(s) | migrate_init_config
migrate_dryrun	| Test new migration file(s) | migrate_init_config
