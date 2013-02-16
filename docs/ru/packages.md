# Список пакетов Limb3
Важно: [Что такое пакеты Limb3 и как с ними работать](./packages_architecture.md).

Название пакета | Зависимости	| Назначение
----------------|-------------|-----------
[ACL](../../acl/docs/ru/acl.md) |	CORE | Пакет реализует функционал [ACL](http://ru.wikipedia.org/wiki/ACL)	
[ACTIVE_RECORD](../../active_record/docs/ru/active_record.md) |	CORE, DBAL, VALIDATION | Реализация [паттерна ActiveRecord](http://en.wikipedia.org/wiki/Active_record_pattern). В конечном итоге планируется получить некий аналог ActiveRecord из [Ruby on Rails](http://rubyonrails.org/).		
[CACHE2](../../cache2/docs/ru/cache2.md) | CORE	| Набор классов, предназначенных для обобщения различных техник кеширования	
[CALENDAR](../../calendar/docs/ru/calendar.md) | WACT (опционально) | JavaScript календарь + использующий его WACT тег	
[CLI](https://github.com/r-kitaev/limb/tree/docs/cli) |	CORE | Различные средства, упрощающие разработку CLI интерфейса	
[CMS](../../cms/docs/ru/cms.md) |	WEB_APP |	Пакет предназначен для быстрой разработки административного интерфейса сайта	
[CONFIG](../../config/docs/ru/config.md) | CORE |	Различные средства для работы с конфигурационными файлами	
[CONSTRUCTOR](../../constructor/docs/ru/constructor.md) |	| Генератор кода	
[CORE](../../core/docs/ru/core.md) | | Пакет, отвечающий за поддержку подключения других пакетов. Содержит базовые классы для работы с различными контейнерами данных и коллекциями. Практически все остальные пакеты зависят от него.	
[DATETIME](../../datetime/docs/ru/datetime.md) | CORE	| Пакет для работы с временем, датой, временными периодами и проч.	
[DBAL (database abstraction layer)](../../dbal/docs/ru/dbal.md)| CORE, TOOLKIT, NET | Пакет, абстрагирующий работу с БД	
[FS](../../fs/docs/ru/fs.md) | CORE	| Различные срества для работы с файловой системой: базовые файловые операции, нахождения файлов по алиасам и др.	
[FILTER_CHAIN](../../filter_chain/docs/ru/filter_chain.md) | CORE	| Имплементация паттерна Intercepring Filter.	
[I18N](../../i18n/docs/ru/i18n.md) | CORE, TOOLKIT, VALIDATION, CONFIG, UTIL, CLI, DATETIME	| Пакет, упрощающий процесс интернационализации приложений	
[IMAGEKIT](../../imagekit/docs/ru/imagekit.md) | CORE	| Примитивные средства, абстрагирующие работу с графической библиотекой.
[JS](https://github.com/r-kitaev/limb/tree/docs/js) | | Пакет, содержащий JavaScript средства для модульной загрузки кода и набор базовых JavaScript классов	
[LOG](../../log/docs/ru/log.md)	| CORE | Средства логирования	
[MACRO](../../macro/docs/ru/macro.md) |	CORE, FS | Шаблонизатор MACRO	
[MAIL](../../mail/docs/ru/mail.md) | CORE, VIEW [опционально] | Обертка PHPMailer библиотеки, предназначенной для отсылки почты	
[NET](../../net/docs/ru/net.md) | CORE, UTIL | Набор классов в для работы с сетевыми протоколами (в основном с HTTP)	
[SEARCH](https://github.com/r-kitaev/limb/tree/docs/search) | CORE, DBAL, I18N	| Средства для организации индексирования и поиска (пакет длительное время не находится в разработке)	
[SESSION](../../session/docs/ru/session.md) |	CORE, DBAL | Средства для работы с PHP сессиями и абстрагирования источника хранения сессионных данных (пока только ДБ).	
[TASKMAN](../../taskman/docs/ru/taskman.md) | |	Программная оболочка для выполнения связанных между собой задач	
[TESTS_RUNNER](../../tests_runner/docs/ru/tests_runner.md) | |  Тестовая оболочка, основанная на [SimpleTest](http://www.simpletest.org/), позволяющая организовать группы тестов, используя расположение тестов в файловой системе	
[TOOLKIT](../../toolkit/docs/ru/toolkit.md)	| CORE | Средства для организации Dependency Injection (реализация Dynamic Service Locator)	
[TREE](../../tree/docs/ru/tree.md) | CORE, TOOLKIT, DBAL, VALIDATION, CACHE |	Пакет для организации хранения деревьев в БД (пока поддерживается только materialized path)	
[VALIDATION](../../validation/docs/ru/validation.md) | CORE	| Пакет, предоставляющий различные средства валидации данных	
[VIEW](https://github.com/r-kitaev/limb/tree/docs/view) | WACT [опционально], MACRO [опционально] | Пакет, содержащий средства для работы с представлением	
[WEB_APP](../../web_app/docs/ru/web_app.md)	| CORE, CONFIG, FILE_SCHEMA, TOOLKIT, I18N, WACT, CLI, CACHE, DBAL, VALIDATION, ACTIVE_RECORD, SESSION, MAIL, DATETIME, FILTER_CHAIN, NET	| Набор различных средств для построения web ориентированных приложений	
[WEB_CACHE](../../web_cache/docs/ru/web_cache.md)	| CORE, WEB_APP	| Средства для полностраничного кеширования в web приложении (пакет длительное время не находится в разработке, скорее всего, будет объединен с пакетом CACHE)	
[WEB_SPIDER](https://github.com/r-kitaev/limb/tree/docs/web_spider) | CORE, NET | Средства для организации обхода web контента по ссылкам (пакет длительное время не находится в разработке)	
[WYSIWYG](../../wysiwyg/docs/ru/wysiwyg.md) |	CORE, WEB_APP, WACT	| Расширенный текстовый редактор для ввода гипертекста.

## Инкубатор
«Инкубатором» в Limb3 называется отдельный репозиторий, в который помещаются пакеты, которые пока не «доросли» до релиза. Получить содержимое инкубатора можно через SVN:

    git clone git://github.com/limb-php-framework/limb-incubator.git

Название пакета | Назначение
----------------|-----------
[ZFSEARCH](./tutorials/zend_search.md) | Интеграция Zend_Search с Limb3
