# Пример установки каркасного приложения Limb3
Установка (подготовка кода к работе) Limb3 несложна. Здесь и далее речь пойдет о создании проектов на базе [пакета WEB_APP](../web_app.md), т.к. самый частый случай использования Limb3, в роли веб-приложения.

## 1. Исходный код скелета
Итак, чтобы подготовить ваш новый проект к работе, создайте где-нибудь на диске директорию с названием вашего проекта, например **/var/www/limbapp**.

Скопируйте содержимое папки **limb/web_app/skel** в выбранную директорию проекта.

Скопируйте исходный код limb в папку **/var/www/limbapp/lib/limb**.

## 2. Права
Задайте права на запись директории /var/www/limbapp/var для вебсервера, проще всего это сделать так:

    $ chmod 777 /var/www/limbapp/var

Если этой папки нет, то создайте ее.

## 3. Настройка веб-сервера
### 3.1 Apache
Осталось настроить виртуальный хост, например так:

* Создайте виртуальный хост limbapp в настройках Apache(например, в httpd.conf) примерно такого содержания:

    <VirtualHost *>
      DocumentRoot /var/www/limbapp/www/
      ServerName limbapp
      ErrorLog logs/limbapp-error_log
      CustomLog logs/limbapp-access_log common
    </VirtualHost>
    
* Пропишите ip адрес хоста «limbapp» в файле /etc/hosts(или %WINDOWS%/system32/drivers/etc/hosts в Windows):

    127.0.0.1  limbapp

* Перезапустите Apache

### 3.2 Nginx + php-fpm
Инструкция по установке php-fpm доступна в официальной wiki — [http://php-fpm.org/wiki/RU:Documentation](http://php-fpm.org/wiki/RU:Documentation).

Настройки nginx:

    server {
      listen       80;
      server_name  limbapp;
      index        index.php;
      root         /var/www/limbapp/www;
  
      location / {
        if (!-e $request_filename) {
          rewrite ^(.*)$ /index.php last;
        }
      }

      location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME  $document_root$fastcgi_script_name;

        fastcgi_param  QUERY_STRING       $query_string;
        fastcgi_param  REQUEST_METHOD     $request_method;
        fastcgi_param  CONTENT_TYPE       $content_type;
        fastcgi_param  CONTENT_LENGTH     $content_length;

        fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
        fastcgi_param  REQUEST_URI        $request_uri;
        fastcgi_param  DOCUMENT_URI       $document_uri;
        fastcgi_param  DOCUMENT_ROOT      $document_root;
        fastcgi_param  SERVER_PROTOCOL    $server_protocol;

        fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
        fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

        fastcgi_param  REMOTE_ADDR        $remote_addr;
        fastcgi_param  REMOTE_PORT        $remote_port;
        fastcgi_param  SERVER_ADDR        $server_addr;
        fastcgi_param  SERVER_PORT        $server_port;
        fastcgi_param  SERVER_NAME        $server_name;

        fastcgi_param  REDIRECT_STATUS    200;
      }
    }

## 4. Shared-файлы
В некоторые пакеты (CMS, WYSIWYG, JS, CALENDAR) входят файлы, которые должны быть доступны из веба. Эти файлы лежат в папках **shared**, соответствующих пакетов. Например, файл **limb/cms/shared/js/app.js** должен быть доступен по адресу http://limbapp/shared/cms/js/app.js. Поддержку shared-папок можно реализовать, как минимум тремя способами:

* простым копированием содержимого
* символическими ссылками
* настройками веб-сервера

## 5. Часто используемые классы
Модифицируем немного файл setup.php:

    [...]
    //Подключим наиболее популярные файлы
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
    lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

lmbController и lmbActiveRecord самые часто используемые классы в нашем приложении. lmb_require() использует отложенную загрузку классов, так что сильного влияния на производительность эти две строки не будут иметь.
