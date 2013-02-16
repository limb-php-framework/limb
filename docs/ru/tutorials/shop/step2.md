# Шаг 2. Запуск приложения. База данных. Базовые шаблоны
## Создание проекта
Наше приложение будет базироваться на базе пакета [CMS](../../../../cms/docs/ru/cms.md). Создать приложение можно двумя способами:

* автоматическим, при помощи задачи **project_create** утилиты [limb.php](../../../../taskman/docs/ru/taskman/limb.php.md)
* вручную, следуя [инструкции](../../cms/cms_manual_setup.md)

## Файл setup.php проекта
Модифицируем немного файл shop/setup.php:

    <?php
    [...]
    //Подключим наиболее популярные файлы
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
    lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

lmbController и lmbActiveRecord самые часто используемые классы в нашем приложении. lmb_require() использует отложенную загрузку классов, так что сильного влияния на производительность эти две строки не будут иметь.

А так же создадим файл setup.override.php, для упрощения разработки и отладки:

    lmb_env_setor('LIMB_APP_MODE', 'devel');

## База данных
Проанализировав требования к нашему приложению, можно сделать вывод, что нам потребуется хранить в базе данных следующие сущности:

* Товары — таблица **product**.
* Зарегистрированные пользователи — таблица **user**.
* Заказы — таблица **order**.
* Позиции заказов — таблица **order_line**.

Все сущности мы будем реализовывать при помощи класса lmbActiveRecord пакета ACTIVE_RECORD. Напомним, что условием использования ACTIVE_RECORD является наличие автоинкременстного поля **id** в таблице.

Таблица **product**:

    CREATE TABLE `product` (                    
     `id` INT(11) NOT NULL AUTO_INCREMENT,  
     `title` VARCHAR(255) DEFAULT NULL,        
     `description` text,                       
     `is_available` tinyint(1) DEFAULT NULL,   
     `price` FLOAT DEFAULT NULL,               
     `image_name` VARCHAR(255) DEFAULT NULL,
      PRIMARY KEY  (`id`)                       
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    CREATE TABLE `user` (                            
     `id` INT(11) NOT NULL AUTO_INCREMENT,       
     `name` VARCHAR(255) DEFAULT NULL,              
     `login` VARCHAR(30) DEFAULT NULL,              
     `hashed_password` VARCHAR(32) DEFAULT NULL,  
     `email` VARCHAR(255) DEFAULT NULL,
     `address` text,           
      PRIMARY KEY  (`id`)                            
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;   
 
    CREATE TABLE `order` (                        
     `id` INT(11) NOT NULL AUTO_INCREMENT,    
     `user_id` INT(11) NOT NULL DEFAULT '0',  
     `date` INT(11) NOT NULL DEFAULT '0',     
     `summ` FLOAT DEFAULT NULL,                  
     `status` INT(11) DEFAULT NULL,              
     `address` VARCHAR(255) DEFAULT NULL,           
     PRIMARY KEY  (`id`),                        
     KEY `user_id` (`user_id`)                   
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
 
    CREATE TABLE `order_line` (                    
     `id` INT(11) NOT NULL AUTO_INCREMENT,     
     `order_id` INT(11) NOT NULL DEFAULT '0',  
     `product_id` INT(11) DEFAULT NULL,        
     `quantity` INT(11) DEFAULT NULL,                
     `price` INT(11) DEFAULT NULL,                
     PRIMARY KEY  (`id`),                         
     KEY `order_id` (`order_id`),                 
     KEY `product_id` (`product_id`)              
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Нам необходимо сохранить цену товара на момент покупки, поэтому таблица order_line имеет поле **price**.

Если вы выбрали копию готового примера магазина, то SQL-код структуры базы данных можно найти в файле **shop/init/db.mysql**.

Не забудьте настроить параметры подключения к БД в файле **shop/settings/db.conf.php**.

## Папка для хранения изображений товаров
Создайте папку shop/www/product_images/. В этой папке мы будем хранить изображения для наших товаров.

Убедитесь, что веб-сервер имеет права на чтение и запись в эту папку.

Также скопируйте файл shop/www/images/no_image.gif в соответствующую папку своего проекта. Это изображение будет использоваться для товаров, у которых не будет изображения.

Теперь добавим в shop/setup.php переменную окружения PRODUCT_IMAGES_DIR, в которой будем хранить абсолютный путь до папки с изображениями:

    [...]
    lmb_env_setor('PRODUCT_IMAGES_DIR', dirname(__FILE__) . '/www/product_images/');
    [...]

## Базовые шаблоны
Мы начнем наше приложение с панели управления на базе пакета CMS. Для панели управления все базовые шаблоны и контроллеры уже созданы при создании базового приложения.

* Основной контроллер панели управления: **limb/cms/src/controller/AdminController.class.php**
* Шаблоны для главной страницы панели управления: **limb/cms/template/admin/display.phtml**
* Основной шаблон, который будет базой (базовый шаблон — враппер) для всех страниц панели управления: **limb/cms/template/admin_page_layout.phtml**

Надеемся, что MACRO-теги [{{wrap}}](../../../../macro/docs/ru/macro/tags/core_tags/wrap_tag.md) и [{{slot}}](../../../../macro/docs/ru/macro/tags/core_tags/slot_tag.md) вам уже знакомы по первому примеру. Вы также можете подробнее прочитать [про композицию MACRO-шаблонов](../../../../macro/docs/ru/macro/template_composition.md) чтобы вспомнить, как в MACRO шаблоны собираются в единое целое из различных частей.

Теперь создадим шаблон для главной фронтальной страницы нашего приложения.

Файл shop/template/main_page/display.phtml:

    <? $this->title = 'Main page'; ?>
    {{wrap with="front_page_layout.phtml" into="content_zone"}}
    Welcome to our bookstore!
    {{/wrap}}

И основной шаблон, который будет базой для всех страниц магазина.

Файл shop/template/front_page_layout.phtml:

    <html>
    <head>
      <title>{$#title} :: Limb3 shop example application on &#123;&#123;macro&#125;&#125;</title>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <link rel=stylesheet type="text/css" href="http://bits.limb-project.com/shop/styles/main.css"/>
    </head>
    <body>
 
    <div id="header">
      <div class="center">
        <img src="http://bits.limb-project.com/shop/images/logo.limb.gif"  width='384' height='46' alt='logo.limb' id='logo'/>
        <div id="limb_links"><a href="http://limb-project.com">limb-project.com</a>&nbsp;|&nbsp;<a href="http://bits.limb-project.com">bits.limb-project.com</a></div>
      </div>
    </div>
 
     <div id="center">
      <div id="wrapper" >
        <div id="container">
          <div id="content">
            <h1>{$#title}</h1>
            {{slot id='content_zone'/}}
          </div>
        </div>
 
        <div id="sidebar">
          <div id="navigation">
            <ul>
              <li><a href="/product/">Products</a></li>
              <li><a href="/cart/">Your Cart</a></li>
              <li><a href="/user/login">Login</a></li>
            </ul>
          </div>
 
          <dl id="profile">
            <dt>Profile</dt>
            <dd>
              Not yet implemented.
            </dd>
          </dl>
        </div>
      </div>
    </div>
    </body>
    </html>

Область, которая лежит в <dl id='profile'> мы пока оставили пустой. Профилем пользователя мы займемся на шаге 4.

## Первый взгляд на проект
Далее мы очищаем кеш в папке shop/var/ и пробуем зайти на страницу http://your_shop_example_domain/admin/ (логин admin, пароль secret) и http://your_shop_example_domain/.

## Шаблон для 404 ошибки
Создадим также шаблон красиво отображающий 404 ошибку (страница не найдена):

Файл shop/template/not_found.phtml:

    <? $this->title = 'Not found'; ?>
    {{wrap with="front_page_layout.phtml" in="content_zone"}}
    <b>Error 404.</b>
    <p>Page not found.</p>
    {{/wrap}}

Шаблон not_found.phtml используется по-умолчанию контроллером NotFoundController (который можно найти в папке limb/web_app/src/controller/).

Попробуйте зайти на несущесвующую страницу, например /no_such_page, и вы увидите как отработает именно этот шаблон (возможно вам придется еще раз почистить кеш shop/var/).

## Далее
Следующий шаг — [Шаг 3. Пользователи и все, что с ними связано](./step3.md).
