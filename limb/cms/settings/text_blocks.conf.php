<?php

$entry_text_main_page = <<<EOD
<p>Добро пожаловать на сайт</p>
EOD;

$contact_page = <<<EOD
<p>Контактная информация</p>
EOD;

$footer =  <<<EOD
<p>Подвал</p>
EOD;

$conf = array(

  'entry_text_main_page' => array('title' => 'Приветственный текст на главной странице',
                                  'content' => $entry_text_main_page),

  'contact_page' => array('title' => 'Контактная информация',
                          'content' => $contact_page),
						  
  'footer' => array('title' => 'Подвал Сайта',
                    'content' => $footer),
  
);
