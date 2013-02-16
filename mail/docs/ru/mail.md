# Пакет MAIL
Пакет mail предназначен для отправки почты, как с помощью известной библиотеки PHPMailer, так и стандартными средствами ОС. Помимо этого, опционально, пакет предоставляет средства для использования шаблонов [MACRO](../../../macro/docs/ru/macro.md) в качестве шаблонов писем, а так же средства отладки.

## Пример отправки письма

    $mailer = new lmbMailer();
 
    $recipients = array(
      array("name" => "Sam", "address" => "sam@somehost.com"),
      "todd@somehost.com"
    );
    $sender = 'bob@somehost.com';
    $subject = 'new movie';
    $text = 'Hello';
    $html = '<h1>Hello</h1>';
 
    $mailer->addAttachment('/www/attachments/movie.flv', $name = "Super movie");
    $mailer->embedImage('/www/images/thumb.png', $cid = mt_rand(), $name=" Thumb for super movie");
 
    //отправка текстового письма
    $mailer->sendPlainMail($recipients, $sender, $subject, $text);
 
    //отправка html письма
    $mailer->sendHtmlMail($recipients, $sender, $subject, $html, $text, $charset = 'cp-1251');

##Конфигурация lmbMailer
Находится в settings/mail.conf.php

В старых версиях пакета для конфигурирования lmbMailer использовались константы, заданные с помощью директив define(), а так же функциями lmb_env_set() и lmb_env_setor(). Этот способ хотя и поддерживается, но **настоятельно не рекомендуется**.

Опция конфига | Константа	| Значение по умолчанию	| Описание
--------------|-----------|-----------------------|---------
use_phpmail	| LIMB_USE_PHPMAIL | false | использовать mail() (вместо него будет использоваться SMTP)
smtp_host	| LIMB_SMTP_HOST | 'localhost' | хост SMTP сервера
smtp_port	| LIMB_SMTP_PORT | '25'	| порт SMTP сервера
smtp_auth	| LIMB_SMTP_AUTH | false | использовать ли авторизацию на SMTP сервере
smtp_user	| LIMB_SMTP_USER | - | имя пользователя SMTP
smtp_password	| LIMB_SMTP_PASSWORD	| -	| пароль SMTP

Также при создании объекта lmbMailer можно передать массив c опциями в конструктор. Названия параметров этого массива совпадают с названиями опций конфига. Значения, переданные в конструктор, перекрывают значения взятые из mail.conf.php.

    $custom_config = array(
      'smtp_user' => 'user',
      'smtp_password' => 'password');
 
    $mailer = new lmbMailer($custom_config);

## Разница между отправкой с помощью mail() и smtp
## lmbMailService и мейлеры
### Пример работы с lmbMailService
Например мы хотим отправить пользователю сообщение, что он удачно купил трактор в нашем магазине. Создаем шаблон письма.

Поздравляем с покупкой трактора {#tractor.title}

    Поздравляем!
    Вы, или ваш тайный доброжелатель, только что купили новенький {#tractor.title} на нашем замечательном сайте vtraktore.ru.
    По смешной цене "{$#tractor.price}" рублей. 
    Посмотреть на это чудо инженерного гения можно на странице {$#tractor.profile_url}.

    <h2>Поздравляем!</h2>
    <p>Вы, или ваш тайный доброжелатель, только что купили новенький {#tractor.title} на нашем замечательном сайте vtraktore.ru.</p>
    <p>По смешной цене "{$#tractor.price}" рублей.</p>
    Посмотреть на это чудо инженерного гения можно на странице <a href="{$#tractor.profile_url}">{$#tractor.profile_url}</a>.

Шаблон состоит из трех частей: темы письма, txt-части и html-части (не обязательной). Части разделяются символом \n\n. Назовем шаблон **tractor_sold.phtml** и поместим его в директорию **_mail**, в директории шаблонов (что-то типа template/_mail/tracor_sold.phtml).

Приступим непосредственно к отправке:

    $tractor = new stdClass();
    $tractor->title = 'Советский турбовинтовой мирный трактор, среднего радиуса действия';
    $tractor->price = 4999999;
    $tractor->profile_url = 'http://lurkmore.ru/Мирный_советский_трактор';
 
    $service = new lmbMailService('tractor_sold');
    $service->set('tractor', $tractor);
    $service->sendMailTo('petya-the-pig@gmail.com');

### Классы-мейлеры
Мейлеры это объекты, которые отвечают за непосредственно отправку письма ( интерфейс lmbBaseMailerInterface). В данный момент поддерживаются следующие мейлеры:

1. **lmbMailer** — «честный» мейлер: отправляет письмо в соответствии с настройками mail-конфига
2. **lmbFileMailer** — создает файлы, с содержимым писем, во временной папке limb (по умолчанию это <PROJECT_DIR>/var )
3. **lmbMemoryMailer** — не отсылает писем, а хранит их внутри себя. Удобен для тестов. Для доступа к «отправленным» письмам имеются методы getMailContents() и clearMailContents()
4. **lmbFirePHPMailer** — отправляет письма в консоль firefox-расширения [FireBug](http://getfirebug.com/), используя библиотеку [FirePHP](http://www.firephp.org/)
5. **lmbResponseMailer** — записывает текст письма в объект-ответ (lmbHttpResponse)

### Конфигурация
**Выбор мэйлера для lmbMailService**

В данный момент выбор мейлера происходит в самом lmbMailService. В будущем инстанцирование и настройка мэйлера будет вынесена в [toolkit](../../../toolkit/docs/ru/toolkit/lmb_toolkit.md).

Выбор мэйлера происходит на основе переменной **mode** конфигурационного файла **settings/mail.conf.php** следующим образом:

Значение | Описание
---------|---------
null | По умолчанию. Используется lmbMailer
testing	| Используется lmbMemoryMailer
devel	| Используется lmbResponseMailer
