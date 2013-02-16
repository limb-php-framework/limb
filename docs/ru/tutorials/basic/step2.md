# Шаг 2. Вывод новостей через шаблон
## Создание таблицы в базе данных и заполнение ее данными
Нам необходимо создать пустую базу данных с именем, которые вы указали в файле **settings/db.conf.php**. Пусть это будет limb_crud. Нам необходимо будет внести в базу данных всего одну таблицу для хранения списка новостей.

Вот SQL-код:

    CREATE TABLE `news` (
      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
      `title` VARCHAR(255) NOT NULL DEFAULT '',
      `annotation` text NOT NULL,
      `content` longtext NOT NULL,
      `date` DATE NOT NULL DEFAULT '0000-00-00',
      PRIMARY KEY  (`id`),
      KEY `date` (`date`)
    ) TYPE=MyISAM;

Теперь заполним нашу таблицу тестовыми данными. Например, такими:

    INSERT INTO `news` VALUES (3,'We finished model part','Model is finished.','Model is finished. And more text here!','2006-08-15');
    INSERT INTO `news` VALUES (4,'We finished view partfds','All templates are ready!','We created display.html, detail_display.html, create.html, edit.html, admin_display.html','2006-08-16');
    INSERT INTO `news` VALUES (5,'Controller is finished','Controller is ready now!','The most difficult part was to describe controller!','2006-08-17');
    INSERT INTO `news` VALUES (6,'Basic tutorial is finished and ready to be published','All pages are ready as well as php-code. Now it\'s time to rock\'n\'roll!','','2006-08-18');

Данный sql-код можно найти в файле examples/crud/init/db.mysql. (для SQLite соответственно — examples/crud/init/db.sqlite)

## Создание класса для работы с таблицей
Создайте папку src/model в приложении и поместите туда файл News.class.php со следующим содержимым:

    <?php
    lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
 
    class News extends lmbActiveRecord
    {
    }
    ?>

Класс lmbActiveRecord является реализацией [паттерна ActiveRecord](http://en.wikipedia.org/wiki/ActiveRecord), суть которого заключается в следующем: существует класс, который является контейнером данных и отвечает за запись и считывание этих данных из определенной таблицы базы данных; при необходимости в этот класс добавляется функциональность по обработке этих данных.

Класс lmbActiveRecord располагается в [пакете ACTIVE_RECORD](../../../../active_record/docs/ru/active_record.md).

Класс News автоматически по своему имени определит, что ему нужно работать с таблицей news.

## Создание контроллера для новостей
Для того, чтобы нам начать работать с лентой новостей, нам нужно создать контроллер новостей. Термин **контроллер** — можно трактовать по-разному, однако в нашем случае - это класс, который знает, что нужно делать при запросах пользователя к определенному функционалу, например, при запросах к ленте новостей.

Создайте папку src/controller и поместите туда файл **NewsController.class.php** со следующим содержимым:

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
 
    class NewsController extends lmbController
    {
    }
    ?>

## Создание шаблона для вывода списка новостей
Создайте папку template/news и поместите в нее файл display.phtml со следующим содержимым:

    <html>
    <head>
      <title>Newsline</title>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    </head>
    <body>
    <h1>Newsline.</h1>
    </body>
    </html>

Теперь наберите в браузере путь http://%tutorial_address%/news и вы должны увидеть страницу с надписью Newsline. Если это так, то все работает как надо и мы продолжаем.

Теперь мы выведем список новостей. Для этого необходимо модифицировать наши контроллер и шаблон следующим образом:

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
 
    lmb_require('src/model/News.class.php');
 
    class NewsController extends lmbController
    {
      function doDisplay()
      {
        $this->news = lmbActiveRecord::find('News');
      }
    }
    ?>


    <html>
    <head>
      <title>Newsline</title>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    </head>
    <body>
    <h1>Newsline.</h1>
 
    {{list using='$this->news' as='$item'}}
      <table border="1">
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Title</th>
      </tr>
     {{list:item}}
      <tr>
        <td>{$item.id}</td>
        <td>{$item.date}</td>
        <td>{$item.title}</td>
      </tr>
      <tr>
        <td colspan='3'>
          {$item.annotation}
        </td>
      </tr>
     {{/list:item}}
      </table>
    {{/list}}
    </body>
    </html>

Limb по умолчанию использует шаблонную систему [MACRO](../../../../macro/docs/ru/macro.md) по этому расширение шаблонов должно быть *.phtml.

Основными элементами MACRO-шаблона являются [теги](../../../../macro/docs/ru/macro/tags.md) и [выражения](../../../../macro/docs/ru/macro/expressions.md). Кратко поясним, какие элементы шаблонной системы здесь использовались:

* [тег list](../../../../macro/docs/ru/macro/tags/list_tags/list_tag.md) является тегом цикла и используется для отображения различных списков. Та часть, содержимого, которая заключена в тег [list:item](../../../../macro/docs/ru/macro/tags/list_tags/list_item_tag.md) повторяется для каждого элемента списка.
* При помощи выражений {$item.date}, {$item.title}, {$item.annotation} выводятся значения полей каждой новости.

В результате мы должны получить на странице /news нечто подобное:
![Alt-simple_list](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:basic:simple_list.png)

MACRO — очень мощный, расширяемый и быстрый шаблонный движок. Ему посвящен отдельный раздел [«Использование шаблонной системы MACRO»](../../../../macro/docs/ru/macro.md). Мы рекомендуем вам ознакомиться с содержимым этого раздела после выполнения этого примера.

Вы можете немного отвлечься в этом месте и прочитать следующие страницы:

* [«Введение в шаблонную систему MACRO»](../../../../macro/docs/ru/macro/intro.md) — расскажет про основные элементы MACRO-шаблонов.
* [«Компиляция и выполнение шаблона»](../../../../macro/docs/ru/macro/important_details.md) — описывает кратко механизм работы MACRO-шаблонизатора. Понимание внутреннего строения шаблонизатора - ключ к его эффективному использованию.

Или же Вы можете продолжить выполнение примера и почитать про MACRO позднее.

## Каким образом отработал шаблон news/display.html

Класс **NewsController** отнаследован от класса **lmbController**. При обращению по адресу /news, приложение автоматически определяет, что необходимо активизировать контроллер news с действием по-умолчанию. Если бы действие было указано, тогда оно шло бы вторым элементом адреса страницы, например, /news/create. По-умолчанию действие равно **display**. **lmbController** ищет у себя метод **do<ActionName>**, например, **doDisplay** и запускает его, если находит. Если же такого метода нет, тогда **lmbController** пытается найти шаблон в папке с относительным именем controller_name/action_name.phtml, а в нашем случае это news/display.phtml, и если его находит — вызывает на отображение. Если же одновременно нужного метода нет и шаблон не найден — отображается 404 ошибка.

В нашем случае есть и шаблон и Action контроллера. Мы добавили в **NewsController** выборку всех новостей из базы данных:

    $this->news = lmbActiveRecord::find('News');

После этого переменная $this→news стала доступна под тем же именем и в шаблоне.

## Что дальше?
Далее: [Шаг 3. Добавление форм для создания, редактирования новостей. Удаление новостей](./step3.md)
