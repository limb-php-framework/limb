# Пакет CORE
Пакет CORE — базовый пакет Limb3. (см. также [Как работать с пакетами Limb](../../../docs/ru/packages_architecture.md))

Содержит:

* [Глобальные функции](./core/global_functions.md)
 * [для подключения классов и поддержки отложенной загрузки кода](./core/lazy_include.md)
 * [для работы с пакетами (lmb_package_*)](./core/global_functions.md)
 * [для работы с переменными окружения (lmb_env_*)](./core/global_functions.md)
 * [для проверки входных параметров](./core/assert_functions.md)
 * [для работы с путями до файлов с учетом include_path](./core/global_functions.md)
 * [для перевода строк из одного стиля написания в другой](./core/global_functions.md)
* [Контейнеры данных](./core/data_containers.md)
 * коллекции (итераторы, или списковые контейнеры данных)
 * едининичные (несписковые контейнеры данных)
* Различные утилититарные классы
 * [Базовый класс для объектов-хранилищ (lmbObject)](./core/lmb_object.md)
 * [для сериализации. Класс lmbSerializable](./core/lmb_serializable.md)
 * [для создания декораторов на лету](./core/decorators.md)
 * [хелпер по работе с массивами (lmbArrayHelper)](./core/lmb_array_helper.md)
 * [для отложенной инициализации объектов (Хендлы). Класс lmbHandle](./core/handles.md)
 * [Объектные формы call_back вызовов. Класс lmbDelegate](./core/delegates.md)
 * [Доступ к системной информации (lmbSys)](./core/lmb_sys.md)
 * прочие

Большинство пакетов Limb3 (кроме TESTS_RUNNER) зависят от этого пакета.
