# Инструкция пользователя
## Введение

* [Если вы новичок в Limb3...](./for_beginners.md) (основные моменты, советы, где искать описания и примеры).
* [Часто задаваемые вопросы (FAQ)](./faq.md)
* [Где и как скачать Limb3?](./how_to_download.md)
* [Разделение Limb3 на пакеты. Что такое пакеты в Limb3?](./packages_architecture.md)

## Создание проекта
## Типичный проект веб-приложения

* [WEB_APP](../../web_app/docs/ru/web_app.md) — набор различных средств для построения web ориентированных приложений
* [Пример установки каркасного приложения для работы с WEB_APP пакетом](../../web_app/docs/ru/web_app/app_installation.md)
* [FILTER_CHAIN](../../filter_chain/docs/ru/filter_chain.md) — имплементация паттерна Intercepring Filter.
* [CORE](../../core/docs/ru/core.md) — пакет, отвечающий за поддержку подключения других пакетов. Содержит базовые классы для работы с различными контейнерами данных и коллекциями
* [Конфигурационные скрипты в Limb3](./setup_files.md)

## Типы данных и работа с ними

* [Контейнеры данных](../../core/docs/ru/core/data_containers.md)
  * коллекции (итераторы, или списковые контейнеры данных)
  * едининичные (несписковые контейнеры данных)
* Различные утилититарные классы
  * [для сериализации. Класс lmbSerializable](../../core/docs/ru/core/lmb_serializable.md),
  * [для создания декораторов на лету](../../core/docs/ru/core/decorators.md),
  * [хелпер по работе с массивами (lmbArrayHelper)](../../core/docs/ru/core/lmb_array_helper.md)
  * [для отложенной инициализации объектов (Хендлы). Класс lmbHandle](../../core/docs/ru/core/handles.md)
  * [Объектные формы call_back вызовов. Класс lmbDelegate](../../core/docs/ru/core/delegates.md)
* [DATETIME](../../datetime/docs/ru/datetime.md) — работа с временем, датой, временными периодами

## Конфигурирование

* [CONFIG](../../config/docs/ru/config.md) — работа с конфигурационными файлами
* [TOOLKIT](../../toolkit/docs/ru/toolkit.md) — средства для организации Dependency Injection (реализация Dynamic Service Locator)

## Модели данных (Mvc)
[ACTIVE_RECORD](../../active_record/docs/ru/active_record.md) — реализация паттерна ActiveRecord

* [Краткое описание (небольшой пример)](../../active_record/docs/ru/active_record/intro.md)
* Базовые возможности
  * [Создание, сохранение, загрузка и удаление объектов](../../active_record/docs/ru/active_record/crud.md)
  * [Поиск и сортировка объектов. find()-методы lmbActiveRecord](../../active_record/docs/ru/active_record/find.md)
  * [Валидация данных в объектах](../../active_record/docs/ru/active_record/validation.md)
* Отношения между классами
  * [Один-к-одному](../../active_record/docs/ru/active_record/one_to_one.md)
  * [Один-ко-многим](../../active_record/docs/ru/active_record/one_to_many.md)
  * [Много-ко-многим](../../active_record/docs/ru/active_record/many_to_many.md)
  * [Дополнительная информация по отношениям](../../active_record/docs/ru/active_record/more_on_relations.md)
* Дополнительные возможности
  * [Жадная загрузка или Eager fetching](../../active_record/docs/ru/active_record/eager_fetching.md)
  * [Поддержка наследования.](../../active_record/docs/ru/active_record/inheritance.md)
  * [События в ActiveRecord. Расширение поведения базового класса lmbActiveRecord при сохранении и удалении.](../../active_record/docs/ru/active_record/enhancement.md)
  * [Создание новых find() методов. Выполнение своих sql-запросов.](../../active_record/docs/ru/active_record/sql_exec.md)
  * [Использование своего подключение к базе данных (connection) при работе с ActiveRecord](../../active_record/docs/ru/active_record/connection.md)
  * [Отложенная загрузка некоторых атрибутов. Lazy Attributes](../../active_record/docs/ru/active_record/lazy_attributes.md)
  * [ValueObjects](../../active_record/docs/ru/active_record/value_objects.md)
  * [Расширение классов коллекций связей вида один-ко-многим и много-ко-многим.](../../active_record/docs/ru/active_record/how_to_extend_collections.md)
  * [Автоматическое сохранение даты создания и обновления объекта ActiveRecord](../../active_record/docs/ru/active_record/ctime_utime.md)
  * [Использование ACTIVE_RECORD в шаблонах MACRO.](../../active_record/docs/ru/active_record/in_macro_templates.md)
  * [Использование ACTIVE_RECORD в шаблонах WACT.](../../active_record/docs/ru/active_record/in_wact_templates.md)
  * [Обработка даты в объектах Active Record](../../active_record/docs/ru/active_record/datetime_processing.md)

[VALIDATION](../../validation/docs/ru/validation.md) — средства валидации данных

## Контроллеры, экшены и роутинг (mvC)

* [VIEW](https://github.com/r-kitaev/limb/tree/docs/view) — работа с представлением, компонентом MVC

## Шаблоны (mVc)

* [MACRO](../../macro/docs/ru/macro.md) — шаблонизатор MACRO
  * Основные элементы шаблона
      * [Выражения. Вывод значений переменных в шаблоне](../../macro/docs/ru/macro/expressions.md)
      * [Фильтры. Модификация значений при выводе](../../macro/docs/ru/macro/filters_intro.md)
      * [Использование php-кода в шаблонах](../../macro/docs/ru/macro/php_code_in_templates.md)
* Базовые средства шаблонизации MACRO
  * [Вывод списков](../../macro/docs/ru/macro/list_tags.md)
  * [Постраничный вывод списков](../../macro/docs/ru/macro/pagination.md)
  * [Теги форм и элементов форм](../../macro/docs/ru/macro/form_tags.md)
  * [Композиция шаблонов (включение, обворачивание и переиспользование шаблонов)](../../macro/docs/ru/macro/template_composition.md)
* [Полный словарь MACRO-тегов](../../macro/docs/ru/macro/tags.md)
* [Полный словарь MACRO-фильтров](../../macro/docs/ru/macro/filters.md)

## Логгирование
##Разделение прав
[ACL](../../acl/docs/ru/acl.md) — функционал [ACL](http://ru.wikipedia.org/wiki/ACL)

## Низкоуровневая работа с БД

* [DBAL (database abstraction layer)](../../dbal/docs/ru/dbal.md) — пакет, абстрагирующий работу с БД
  * [Введение в пакет DBAL](../../dbal/docs/ru/dbal/intro.md)
  * [Подключение к базе данных](../../dbal/docs/ru/dbal/connection.md)
  * [Использование классов из подсистем Query и Criteria](../../dbal/docs/ru/dbal/intro_to_query_and_criteria.md)
  * [Поддержка транзакций](../../dbal/docs/ru/dbal/transactions.md)
  * [Загрузка sql-дампа базы данных](../../dbal/docs/ru/dbal/dump_load.md)

## Тестирование

* [TESTS_RUNNER](../../tests_runner/docs/ru/tests_runner.md) — тестовая оболочка, основанная на SimpleTest, позволяющая организовать группы тестов, используя расположение тестов в файловой системе
  * [limb_unit — консольный инструмент для выполнения SimpleTest тестов](../../tests_runner/docs/ru/tests_runner/limb_unit.md)

## Оптимизация производительности
[CACHE2](../../cache2/docs/ru/cache2.md) — обобщение различных техник кеширования

## Дополнительные возможности

* [IMAGEKIT](../../imagekit/docs/ru/imagekit.md) — работа с графическими библиотеками (imagick, gd).
* [FS](../../fs/docs/ru/fs.md) — работа с файловой системой: базовые файловые операции, нахождения файлов по алиасам и др.
* [I18N](../../i18n/docs/ru/i18n.md) — интернационализация приложений
* [JS](https://github.com/r-kitaev/limb/tree/docs/js) — JavaScript средства для модульной загрузки кода и набор базовых JavaScript классов
* [CALENDAR](../../calendar/docs/ru/calendar.md) — JavaScript календарь
* [MAIL](../../mail/docs/ru/mail.md) — отправка почты
* [NET](../../net/docs/ru/net.md) — работа с сетевыми протоколами(в основном с HTTP)
* [SESSION](../../session/docs/ru/session.md) — cредства для работы с PHP сессиями и абстрагирования от источника хранения сессионных данных
* [TREE](../../tree/docs/ru/tree.md) — организации хранения деревьев в БД
* [WYSIWYG](../../wysiwyg/docs/ru/wysiwyg.md) — расширенный текстовый редактор для ввода гипертекста (fckeditor, tinymce)

## Заключение

* [Основные конфигурационные константы используемые в Limb3](./constants.md)
* [Стандарты кодирования Limb3](./coding_standards.md)
* [Работа с репозиторием Limb3](./svn.md)
* [Как запустить тесты на пакеты Limb3](./how_to_run_tests.md)
* [для работы с пакетами (lmb_package_*)](../../core/docs/ru/core/global_functions.md)
* [для работы с переменными окружения (lmb_env_*)](../../core/docs/ru/core/global_functions.md)
