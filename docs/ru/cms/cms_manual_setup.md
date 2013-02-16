# Ручная установка пакета CMS
## Установка каркаса приложения
Для начала необходимо выполнить [установку каркасного веб-приложения](../../../web_app/docs/ru/web_app/app_installation.md).

## Настройка БД
Для начала создадим базу данных:

    $mysql -uroot -p
    > create database limb_site default charset=utf8;

Создадим файл с настройками БД (файл будет расположен **/work/limb_site/settings/db.conf.php**):

    <?php
      $conf = array('dsn' => 'mysql://root:test@localhost/shop?charset=utf8');

Теперь загрузим в базу таблицы, необходимые в работе пакета CMS:

    mysql -uroot -p shop < /work/limb/cms/init/db/mysql

## Правка файлов скелета
Изменим **src/LimbApplication.class.php**

    lmb_require('limb/cms/src/lmbCmsApplication.class.php');
 
    class LimbApplication extends lmbCmsApplication {}

Так же изменим **setup.php**:

    ...
    lmb_package_require('cms'); //вместо lmb_package_require('web_app');
    ...

Теперь вы можете управлять вашим сайтом http://localhost/admin. Логин: admin, пароль: secret.
