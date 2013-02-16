# Введение в пакеты Limb3
[Список пакетов Limb3](./packages.md)

Limb3 — это фреймворк, разделенный на пакеты. Что считается пакетом? Пакет — это, по сути, некоторый узкоспециализированный функционал, расположенный в директории с определенной структурой (см. ниже). Например, есть базовый пакет [CORE](../../core/docs/ru/core.md), пакет шаблонной системы [MACRO](../../macro/docs/ru/macro.md), пакет для отображения объектов в реляционную БД [ACTIVE_RECORD](../../active_record/docs/ru/active_record.md), пакет интернационализации [I18N](../../i18n/docs/ru/i18n.md), пакет для работы с деревьями в базе данных [TREE](../../tree/docs/ru/tree.md) и т.д.

Приложение, построенное при помощи Limb3, таким образом, использует те или иные пакеты и, к тому же, иногда само является пакетом.

## Стандартная файловая структура пакета.

Папка | Описание
------|---------
build/ | Различные build скрипты
cli/ | CLI утилитарные скрипты
examples/ | Примеры использования
lib/ | Внешние библиотеки, от которых зависит пакет
init/ | Инициализационные данные, к примеру, sql файлы для заполнения базы данных
settings/ | Файлы настройки, со значениями по умолчанию
shared/ | Файлы, которые используются во многих проектах, но не являющиемся php-файлами. Обычно здесь хранятся javascript-ы, изображения, css-файлы.
src/ | Исходный код пакета: классы, модули и проч.
template/ | Файлы шаблонов
tests/ | Модульные тесты пакета
www/ | Директория доступная для web сервера, если пакет является web приложением
common.inc.php | Файл инициализации пакета

## Пример подключения файлов из пакетов Limb3

1. Добавлении директории, содержащая Limb3, в параметр PHP [include_path](http://ru2.php.net/manual/en/ini.core.php#ini.include-path).
2. Вместо стандартных require_once/include_once рекомендуется использовать аналогичную по функционалу lmb_require, которая поддерживает отложеную загрузку кода при помощи механизма [__autoload](http://ru2.php.net/manual/en/language.oop5.autoload.php)

Например:

    <?php
    set_include_path('/path/to/limb/packages/parent/dir/' . PATH_SEPARATOR  . get_include_path()); 
 
    // Подключение класса lmbUri из пакета NET
    require_once('limb/net/http/lmbUri.class.php');
 
    // Подключение класса lmbUri из пакета NET с использованием оптимизированной версии require_once 
    // с поддержкой отложенной загрузки кода, т.е. в этот момент класс на самом деле еще не загружен,
    // подключение класса происходит только в момент вызова new lmbUri при помощи механизма __autoload
    lmb_require('limb/net/http/lmbUri.class.php');
 
    $uri = new lmbUri('http://test.com');

## Пример подключения пакета Limb3

Стандартный способ работы с пакетами заключается в:

1. Добавлении директории, содержащая Limb3, в параметр PHP include_path.
2. Подключении [ядра Limb3 (пакета CORE)](../../core/docs/ru/core.md).
3. Использовании [функций lmb_package_*](../../core/docs/ru/core/global_functions.md) для подключения пакетов.

Пример:

    <?php
    set_include_path('/path/to/limb/packages/parent/dir/' . PATH_SEPARATOR . get_include_path());   //добавление директории
    require('limb/core/common.inc.php'); //подключение ядра
 
    lmb_package_require('active_record'); //подключение пакета WEB_APP
 
    var_dump(lmb_packages_list()); //вывод списка подключенных пакетов

Вывод будет примерно таким:

    korchasa@korchasa:/www/limb$ php ./packages_example.php 
    array(4) {
      [0]=> string(7) "toolkit"
      [1]=> string(4) "dbal"
      [2]=> string(10) "validation"
      [3]=> string(13) "active_record"
    }

## Пример создания собственного пакета
ВНИМАНИЕ! Эта часть API еще не «устоялась» и может быть изменена. Подключение пакета означает подключение его **common.inc.php** файла. В нем пакет должен «подтвердить» подключение с помощью функции lmb_package_register. Пример common.inc.php (например. наш пакет называется **captcha**):

    <?php
    lmb_package_require('net');
    // ... инициализация пакета ....
    lmb_package_register('captcha', dirname(__FILE__));

Помимо контроля над пакетами и их зависимостями, подключение пакетов дает возможность немного упростить подключение классов. Например:

    lmb_package_require('captcha', '/usr/local/lib/php/my_limb');
    lmb_require_package_class('captcha', 'Captcha'); //буден подключен файл /usr/local/lib/php/my_limb/captcha/src/Captcha.class.php

Помимо этого использование lmb_require_package_class() лучше с точки зрения производительности, т.к. во-первых использует абсолютные пути, а во-вторых вместо возможных нескольких путей из get_include_path() используется всего один.
