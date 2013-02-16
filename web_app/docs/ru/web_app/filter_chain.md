# Использование цепочки фильтров для организации Front-Controller
## Что такое цепочка фильтров приложения?
Типичное веб-приложение, построенние на базе Limb3 пакета WEB_APP, представляет из себя один класс Application. Например, рассмотрим файл **/www/index.php** из CRUD примера, к которому перенаправляются все запросы к нашему приложению.

    <?php
    require_once(dirname(__FILE__) . '/../setup.php');
    require_once('limb/web_app/src/lmbWebApplication.class.php');
 
    $application = new lmbWebApplication();
    $application->process();
    ?>

index.php по сути подключает настроечный файл setup.php и запускает объект класса lmbWebApplication на исполнение. **lmbWebApplication** — это центральный класс, само приложение. С него начинается обработка запроса пользователя.

Что представляет из себя lmbWebApplication?

Файл limb/web_app/src/lmbWebApplication.class.php:

    <?php
    lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
    lmb_require('limb/classkit/src/lmbHandle.class.php');
 
    class lmbWebApplication extends lmbFilterChain
    {
      function __construct()
      {
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbUncaughtExceptionHandlingFilter'));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbRequestDispatchingFilter',
                                            array(new lmbHandle('limb/web_app/src/request/lmbRoutesRequestDispatcher'))));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));
      }
    }
    ?>

lmbWebApplication — это **цепочка фильтров**, содержащая какой-то предопределенный набор фильтров. lmbWebApplication — это класс-пример, который подходит для весьма простых приложений. В реальных приложениях обычно всегда используются свои собственные подобные классы.

Если вы знакомы с шаблоном проектирования Intercepting Filter, то понять идею цепочки фильтров из Limb3 не составит большого труда.

Итак, **фильтры** являются своеобразными расширениями ядра системы, из них состоит самая «верхняя» (начальная) часть приложения, созданного на базе Limb3. Фильтры выполняют действия, характерные для большинства приложений, но обычно не имеющих никакого отношения к предметной области приложения (различные вспомогательные действия). Например, фильтры могут стартовать сессию, разбирать запрос, проверять права доступа, выдавать закешированный вариант страницы, регистрировать посещение страницы в статистике и т.д. То есть фильтры — это своеобразный [FrontController](http://en.wikipedia.org/wiki/FrontController), только в Limb3 он не имеет формы единого класса, а разделен на составляющие, любую из которых можно заменить. Количество фильтров различно от приложения к приложению.

Внутренняя логика каждого фильтра зависит от ситуации. Фильтры самостоятельно принимают решение о том, передавать управление следующим фильтрам или нет. Это позволяет обрывать нормальный ход работы приложения и, например, переносить пользователей на определенные страницы сайта.

Цепочка фильтров реализована в виде [пакета FILTER_CHAIN](../../../../filter_chain/docs/ru/filter_chain.md).

Большинство стандартных фильтров можно найти в пакете WEB_APP limb/web_app/src/filter. Вот список таких фильтров c небольшими описаниями:

Фильтр | Назначение
-------|-----------
lmbResponseTransactionFilter | Отсылает данные (заголовки и контент) браузеру после того, как большинство остальных фильтров отработали. Необходим, так как логика приложения может требовать обнуления промежуточно сформированных данных и передачи совсем другого результата браузеру.
lmbSessionStartupFilter	| Настраивает драйвер хранилища сессионных данных и стартует сессию
lmbUncaughtExceptionHandlingFilter | Позволяет обрабатывать ошибки, возникшие в системе и выводить пользователю более «мягкие варианты»
lmbRequestDispatchingFilter	| В этом фильтре определяется текущий контроллер и действие.
lmbActionPerformingFilter	| Запускает команду, которая соответствует текущему контроллеру и действию
lmbViewRenderingFilter | Запускает процесс рендеринга шаблона, который был установлен в результате выполнения действия контроллера

## Связь Запроса, Контроллера и View
Как в Limb3 в рамках цепочки фильтров приложения взаимодейтсвуют MVC-компоненты, то есть:

* как приложение определяет, что необходимо делать?
* как осуществляется отработка нужного действия?
* как осуществляется рендеринг шаблона?
* как различные компоненты знают друг о друге?

За первые три пункта этого списка, как вы наверное уже догадались, отвечают соответствующие фильтры:

* **lmbRequestDispatchingFilter**
* **lmbActionPerformingFilter**
* **lmbViewRenderingFilter**

Сменив или расширив любой из этих фильтров, мы можем изменить любой компонент Limb-приложения:

* Сменить шаблонную систему
* Поменять механизм разбора запроса
* Добавить любые дополнительные проверки, характерные для всех (большинства) вызовов.

Теперь последний вопрос: как различные компоненты знают друг о друге? Для этих целей существует [тулкит](../../../../toolkit/docs/ru/toolkit.md)!

Тулкит (через [lmbWebAppTools](./lmb_web_app_tools.md)) содержит:

* переменную **$view**, которая является объектом класса lmbView (вернее производного от него — скорее всего это будет lmbWactView). $view — является посредником между реальным шаблоном и приложением.
* переменную **$dispatched_controller**, которая является объектом класса lmbController (или дочерним).
* объект **$request** (класс [lmbHttpRequest](../../../../net/docs/ru/net/lmb_http_request.md), находится в пакете NET) — представляет из себя контейнер со всеми данными, поступившими в зачестве запроса приложению.
* объект **$response** (класс [lmbHttpResponse](../../../../net/docs/ru/net/lmb_http_response.md), находится также в пакете NET) — представляет из себя ответ приложения на запрос.

Так как тулкит может быть получен в любом месте системы - значит все эти объекты также доступны везде в приложении!

Поэтому:

* lmbRequestDispatchingFilter — использует $request и ставит в тулкит $dispatched_controller.
* lmbActionPerformingFilter — получает из тулкита $dispatched_controller и вызывает метод performAction()
* Контроллер где-то внутри получает $view и ставит в него название шаблона через метод setTemplate($name) (может также передавать во $view некоторые данные)
* lmbViewRenderingFilter — получает $view и вызывает у мего метод render(), а результат рендеринга помещает в $response.

## См. также
[Схема типичного Limb3 web-приложения, выполненого при помощи пакета WEB_APP](./application_workflow.md)
