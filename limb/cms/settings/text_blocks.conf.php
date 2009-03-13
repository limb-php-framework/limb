<?php

$footer =  <<<EOD
<p>Подвал</p>
EOD;

$contact_page = <<<EOD
<p>Контактная информация</p>
EOD;

$entry_text_main_page = <<<EOD
<p>Добро пожаловать на сайт</p>
EOD;

$news_salon = <<<EOD
<p>Тайский массаж традиционный<br/>
Тайский массаж расслабляющий<br/>
Тайский массаж для коррекции фигуры<br/>
Тайский массаж травяной<br/>
Тайский массаж ног</p>
EOD;

$sertificate = <<<EOD
<p>Подарочный сертификат клуба "SPA-салон" - это эксклюзив</p>
EOD;

$conf = array(
  'footer' => array('title' => 'Подвал Сайта',
                    'content' => $footer),

  'contact_page' => array('title' => 'Контактная информация',
                          'content' => $contact_page),

  'entry_text_main_page' => array('title' => 'Приветственный текст на главной странице',
                                  'content' => $entry_text_main_page),

  'news_salon' => array('title' => 'Новинки салона',
                       'content' => $news_salon),

  'sertificate' => array('title' => 'Сертификат',
                          'content' => $sertificate),
);
