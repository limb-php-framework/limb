# Пакет ACTIVE_RECORD
Пакет ACTIVE_RECORD — реализация паттерна [ActiveRecord](http://en.wikipedia.org/wiki/ActiveRecord) отчасти схожего с реализацией подобного паттерна в [RubyOnRails](http://rubyonrails.org/).

## Использование пакета ACTIVE_RECORD
Если вы только начинаете работать с Limb и приступаете к изучению пакета ACTIVE_RECORD, мы советуем читать страницы подряд, сверху вниз. Если вы уже разбираетесь в работе пакета, то можете пользоваться этим разделом, как руководством или справочником. Мы также настоятельно советуем просмотреть тесты на пакет ACTIVE_RECORD, так как тесты содержат исчерпывающую информацию по использованию lmbActiveRecord и других классов пакета.

* [Краткое описание (небольшой пример)](./active_record/intro.md)
* Базовые возможности ActiveRecord
 * [Создание, сохранение, загрузка и удаление объектов](./active_record/crud.md)
 * [Поиск и сортировка объектов. find()-методы lmbActiveRecord](./active_record/find.md)
 * [Валидация данных в объектах](./active_record/validation.md)
* Отношения между классами
 * [Один-к-одному](./active_record/one_to_one.md)
 * [Один-ко-многим](./active_record/one_to_many.md)
 * [Много-ко-многим](./active_record/many_to_many.md)
 * [Жадная загрузка или Eager fetching](./active_record/eager_fetching.md)
 * [Дополнительная информация по отношениям](./active_record/more_on_relations.md)
* Дополнительные возможности
 * [Поддержка наследования](./active_record/inheritance.md)
 * [Загрузка объектов](./active_record/sql_exec.md)
 * [События в ActiveRecord. Расширение поведения базового класса lmbActiveRecord при сохранении и удалении](./active_record/enhancement.md)
 * [Использование своего подключение к базе данных (connection) при работе с ActiveRecord](./active_record/connection.md)
 * [Отложенная загрузка некоторых атрибутов. Lazy Attributes](./active_record/lazy_attributes.md)
 * [Поддержка ValueObjects](./active_record/value_objects.md)
 * [Расширение классов коллекций связей вида один-ко-многим и много-ко-многим](./active_record/how_to_extend_collections.md)
 * [Автоматическое сохранение даты создания и обновления объекта ActiveRecord](./active_record/ctime_utime.md)
* ACTIVE_RECORD и шаблонизаторы
 * [Использование ACTIVE_RECORD в шаблонах MACRO](./active_record/in_macro_templates.md)
* Дополнительные примеры
 * [Создание древовидных структур на основе алгоритма materialized path](./active_record/materialized%20path.md)
 * [Редактирование нескольких объектов при помощи одной формы](./active_record/edit_multiple_objects.md)
 * [Обработка даты в объектах Active Record](./active_record/datetime_processing.md)
* Прочее
 * [Сравнение с Doctrine](./active_record/doctrine.md)
