# Шаг 3. Добавление форм для создания и редактирования новостей. Удаление новостей
## Действие по добавлению новостей
### Шаблон news/create.phtml для добавления новостей
Добавим в папку template/news файл шаблона create.phtml следующего содержимого:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Create news</h1>
 
    {{form id='news_form' name='news_form' method='post'}}
 
    {{label for="title"}}Title{{/label}} : 
    {{input name='title' type='text' size='60' title='Title'/}}<br/>
 
    {{label for="date"}}Date{{/label}} : 
    {{input name='date' type='text' size='15' title='Date'/}}<br/>
 
    {{label for="annotation"}}Annotation{{/label}} : 
    {{textarea name='annotation' rows='2' cols='40' title='Annotation'/}}<br/>
 
    {{label for="content"}}Content{{/label}} : 
    {{textarea name='content' rows='5' cols='40'/}}<br/>
 
    {{input type='submit' value='Create'/}}
 
    {{/form}}
 
    </body>
    </html>

По-сути это обычная hmtl-страница, однако есть кое-какие детали:

* [{{form}}](../../../../macro/docs/ru/macro/tags/form_tags/form_tag.md) — не просто тег <form>, а компонент шаблона с каким-либо активным поведением (какое именно — будет сказано потом). Обычно любым активным компонентам шаблона даются свои идентификаторы, то есть указывается атрибут **id**.
* Обратите внимание, что элементы формы имеют идентификаторы, совпадающие по названию с названиями полей таблицы news.

### Изменения в контроллере NewsController
Теперь расширим наш контроллер **NewsController** чтобы он мог принимать данные с формы и добавлять новые записи в базу данных:

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
    lmb_require('src/model/News.class.php');
 
    class NewsController extends lmbController
    {
      function doCreate()
      {
        if(!$this->request->hasPost())
          return;
 
        $news = new News();
        $news->import($this->request);
 
        $news->save();
 
        $this->redirect();
      }
    }

Мы добавили метод doCreate, который будет вызываться при получении нашим приложением запросов вида /news/create. Шаблон будет выбираться аналогично, то есть template/news/create.phtml, как в случае и с действием по-умолчанию display.

Разберем тело метода doCreate().

    if(!$this->request->hasPost())
      return;

lmbController для удобства по-умолчанию содержит в себе объекты Запроса **request** и Ответа **response** в качестве своих атрибутов и сокращения записи. Здесь мы проверяем, был ли POST запрос с нашей формы. Если нет — тогда мы просто в первый раз зашли на данную страницу и нужно просто отобразить форму.

    $news = new News();
    $news->import($this->request);

Мы создали экземпляр класс News и поместили в него все данные из запроса. News автоматически выберет из запроса только те поля, которые есть в таблице news (немного упрощенное описание, но пока оно нас устраивает). Именно поэтому мы дали полям формы в шаблоне create.phtml имена, совпадающие с названиями полей таблицы news.

    $news->save();

Мы сохраняем новую записи в базу данных и …

    $this->redirect();

перебрасываем пользователя на страницу действия по-умолчанию, то есть на /news

Попробуем набрать в адресной строке /news/create. Должно получиться следующее:
![Alt-simple_create](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:basic:simple_create.png)

## Добавление валидации данных

Нам хотелось бы, чтобы к полям новостей применялись следующие правила:

* поля title, date и annotation были бы обязательными для заполнения,
* title был меньше 75 символов,
* content — заполнялся бы по желанию пользователя.

Для этого нам необходимо будет внести некоторые изменения в классы News, **NewsController** и расширить шаблон create.phtml.

### Изменения в классе News
Итак, теперь класс News будет выглядеть следующим образом:

    <?php
    lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
 
    class News extends lmbActiveRecord
    {
      protected function _createValidator()
      {
        $validator = new lmbValidator();
 
        $validator->addRequiredRule('title');
        $validator->addRequiredRule('annotation');
        $validator->addRequiredRule('date');
 
        lmb_require('limb/validation/src/rule/lmbSizeRangeRule.class.php');
        $validator->addRule(new lmbSizeRangeRule('title', 75));
        return $validator;
      }
    }
    ?>

lmbActiveRecord при вызове метода save() может проверять данные, которые в нее добавили при помощи валидатора. Валидатор создается при помощи метода _createValidator(). По-умолчанию, lmbActiveRecord :: _createValidator() возвращает пустой валидатор. Наша задача — создать свой собственный.

Валидатор — объект класса **lmbValidator**, который располагается в [пакете VALIDATION](../../../../validation/docs/ru/validation.md). Валидатор состоит из набора правил валидации, которые мы в него и добавляем в методе **_createValidator()**.

Обратите внимание, что строку

    $validator->addRequiredRule('title');

можно переписать как

    lmb_require('limb/validation/src/rule/lmbRequiredRule.class.php');
    $validator->addRule(new lmbRequiredRule('title'));

по своей сути эквивалентны, а второй вариант приведен для ясности — в большинстве случаев используется именно сокращеннй первый вариант.

Итого мы использовали 2 правила:

* **lmbRequiredRule** — обязывает поле присутствовать в проверяемом объекте
* **lmbSizeRangeRule** — обязывает поле содержать определенное количество символов. В нашем случае — не больше 75.

### Изменения в классе NewsController
В действие doCreate() контроллера **NewsController** нам нужно будет внести некоторые изменения:

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
    lmb_require('src/model/News.class.php');
 
    class NewsController extends lmbController
    {
      function doCreate()
      {
        if(!$this->request->hasPost())
          return;
 
        $news = new News();
        $news->import($this->request);
 
        $this->useForm('news_form');
        $this->setFormDatasource($news);
 
        if($news->trySave($this->error_list))
          $this->redirect();
      }
    }
    ?>

Обратите внимание на следующие строки:

    $this->useForm('news_form');
    $this->setFormDatasource($news);
 
    if($news->trySave($this->error_list))
      $this->redirect();

При помощи первых двух строк мы связываем активный компонент формы, находящийся в шаблоне с объектом новости. Это позволит нам не терять данные в полях формы, если часть данных была введена неверно.

Метод lmbActiveRecord :: **trySave($error_list)** возвращает true, если все данные были введены верно и false, если существовали ошибки. trySave() является лишь «оберткой» для метода save(): метод save() при обнаружении ошибок валидации генерирует исключение класса lmbValidationException, которое ловится в методе trySave().

### Изменения в шаблоне news/create.html
Теперь добавим в шаблон template/news/create.phtml код для отображения ошибок валидации:

    [...]
    <h1>Create news</h1>
 
    {{form id='news_form' name='news_form' method='post'}}
 
    {{form:errors to='$fields_errors'/}}
 
    {{list using='$fields_errors' as="$error"}}
      <div class="message_error">
        <b class='title'>This fields contained errors</b>
        <ol>
          {{list:item}}
            <li><span style="color:red;">{$error.message}</span></li>
          {{/list:item}}
        </ol>
      </div>
    {{/list}}
 
    {{label for="title"}}Title{{/label}} : {{input name='title' type='text' size='60' title='Title'/}}<br/>
    [...]

Для вывода ошибок мы использовали тег [{{form:errors}}](../../../../macro/docs/ru/macro/tags/form_tags/form_errors_tag.md), который передает список ошибок валидации из формы в компонент, указанный атрибутом to. Ошибки будут выводится при помощи тегов [{{list}}](../../../../macro/docs/ru/macro/tags/list_tags/list_tag.md) и [{{list:item}}](../../../../macro/docs/ru/macro/tags/list_tags/list_item_tag.md).

Ошибки валидации будут попадать в шаблон автоматически, так как мы связали объект класса News с активной формой в методе doCreate() контроллера NewsController.

Теперь попробуем ввести неверные данные и проверить систему валидации:
![Alt-simple_create_errors](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:basic:simple_create_errors.png)

Если вы увидели нечто подобное, значит вы все делаете правильно.

## Действие по редактированию новости
### Шаблон edit.html
Шаблон по редактированию новости template/news/edit.phtml выглядит почти точно также как и template/news/create.phtml:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Edit news</h1>
 
    {{form id='news_form' name='news_form' method='post' }}
 
    {{form:errors to='$fields_errors'/}}
 
    {{list using='$fields_errors' as="$error"}}
      <div class="message_error">
        <b class='title'>These fields contained errors</b>
        <ol>
          {{list:item}}
            <li><span style="color:red;">{$error.message}</span></li>
          {{/list:item}}
        </ol>
      </div>
    {{/list}}
 
 
    {{label for="title"}}Title{{/label}} : {{input name='title' type='text' size='60' title='Title'/}}<br/>
 
    {{label for="date"}}Date{{/label}} : {{input name='date' type='text' size='15' title='Date'/}}<br/>
 
    {{label for="annotation"}}Annotation{{/label}} : {{textarea name='annotation' rows='2' cols='40' title='Annotation'/}}<br/>
 
    {{label for="content"}}Content{{/label}} : {{textarea name='content' rows='5' cols='40'/}}<br/>
 
    {{input type='submit' value='Edit'/}}
 
    {{/form}}
 
    </body>
    </html>

Налицо большое дублирование, которое может быть легко устранено. Мы займемся оптимизацией шаблонов чуть позже.

### Метод NewsController :: doEdit()

Теперь добавим метод doEdit в класс NewsController, чтобы наш контроллер мог правильно отрабатывать действие edit:

    <?php
      [...]
    class NewsController extends lmbController
    {
      [...]
      function doEdit()
      {
        $news = new News((int)$this->request->get('id'));
 
        $this->useForm('news_form');
        $this->setFormDatasource($news);
 
        if(!$this->request->hasPost())
          return;
 
        $news->import($this->request);
 
        if($news->trySave($this->error_list))
          $this->redirect();
      } 
    ?>

Строкой

    $news = new News((int)$this->request->get('id'));

мы загружаем объект новости с определенным идентификатором, который мы получаем из запроса.

Обратите внимание, что мы связали форму в шаблоне с новостью до проверки $this→request→hasPost(), так как нам нужно передавать данные в шаблон в любом случае. При первоначальном отображении формы поля уже будут заполнены полями из загруженной новости.

### Модификация шаблона template/news/display.phtml

Теперь изменим шаблон template/news/display.phtml чтобы вывести ссылку на страницу редактирования новостей. Добавим новую колонку для вывода доступных действий:

    <html>
    <head>
      <title>Newsline</title>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    </head>
    <body>
    <h1>Newsline.</h1>
 
    <a href='{{route_url params="action:create"/}}'>Create news</a>
    <p/>
 
 
 
    {{list using="$#news" as="$item"}}
      <table border="1">
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Title</th>
        <th>Actions</th>
      </tr>
      {{list:item}}
      <tr>
        <td>{$item.id}</td>
        <td>{$item.date}</td>
        <td>{$item.title}</td>
        <td>
          <a href='{{route_url params="action:edit,id:{$item.id}"/}}'>Edit</a>&nbsp;&nbsp;
          <a href='{{route_url params="action:delete,id:{$item.id}"/}}'>Delete</a>
        </td>
      </tr>
      <tr>
        <td colspan='4'>
          {$item.annotation}
        </td>
      </tr>
      {{/list:item}}
      </table>
    {{/list}}
 
    </body>
    </html>

Здесь мы использовали тег [{{route_url}}](../../../../macro/docs/ru/macro/tags/lmb_request_tags/lmb_route_url_tag.md), который формирует URL на основе параметров, которые ему указаны в атрибуте **params**. По сути route_url осуществляет процесс разбора строки запроса вида /news/create в обратном порядке.

Все, что касается деталей разбора запроса выходит за рамки данного туториала, однако если вам интересны правила, на основе которых осуществляется определение текущего контроллера, посмотрите файл limb/web_app/settings/routes.conf.php.

Поясним работу тега {{route_url}}:

    {{route_url params="action:create"/}}

Этот код сформирует строку вида: **news/create**. news будет взят приложением автоматически, так как мы находимся на странице, за которую отвечает контроллер news. create будет взято из пары action:create, где action - это название параметра, а create - значение параметра.

Точно также формируются ссылки на действия edit и delete. Параметры в атрибуте params тега {{route_url}} разделяются через запятую. То есть тег вида {{route_url params="action:edit,id:{$id}"/}} сформирует ссылку вида: **/news/edit/4»**.

Если вы все сделали правильно, тогда вы должны иметь теперь возможность создавать и редактировать новости.

## Удаление новостей
Для удаления объектов lmbActiveRecord используется метод destroy(). Добавим метод NewsController :: doDelete():

    <?php
      [...]
    class NewsController extends lmbController
    {
      [...]
      function doDelete()
      {
        $news = new News((int)$this->request->get('id'));
        $news->destroy();
        $this->redirect();
      }
    }
    ?>

Пояснять код нет необходимости.

## Что дальше?
Далее: [Шаг 4. Оптимизация шаблонов. Добавление постраничного вывода](./step4.md)
