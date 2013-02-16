# Пример интеграции Zend_Search с Limb3
В [ZendFramework](http://framework.zend.com/) есть довольно мощный модуль поиска Zend_Search, который позволяет индексировать контент и производить по нему поиск с приличной скоростью и без участия БД или каких-либо нестандартных PHP расширений. Дабы не изобретать колесо, имеет смысл использовать этот модуль совместно с Limb3.

В примере используются Limb3-2007.1 и ZendFramework-0.8

Ниже приведено описание возможного использование модуля поиска Zend_Search совместно с пакетоми WEB_SPIDER и SEARCH в некотором абстрактном проекте.

## Постановка задачи
Что в итоге должно получиться:

* Шелл скрипт cli/indexer.php, который по cron'у обходит весь веб контент сайта и индексирует его средствами Zend_Search
* Простейший контроллер, который производит поиск по индексу и выставляет результат своего выполнения во view

## Настройка библиотек
Прежде чем начнем, стоит заметить, как правильно настроить include_path для Limb3 и ZendFramework. Где-то в setup.php своего проекта стоит поместить подобную строчку:

    <?php
    set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR .
                     dirname(__FILE__) . '/lib/' . PATH_SEPARATOR .
                     get_include_path());
    ?>

Это подразумевает, что и Limb3 и ZendFramework расположены в директории lib проекта. Однако ставить весь ZF смысла нет — потребуется только директория Search и все ее содержимое и файл Exception.php в корне директории. Выглядит это примерно так:

      `-lib
        `-limb/...
        `-Zend
          `-Search/..
          `-Exception.php

## Индексирующий шелл скрипт
С установками разобрались, перейдем сразу к шелл скрипту.

**cli/indexer.php**

    <?php
    //аргументом для скрипта является url сайта, например http://mysite.com
    if(!isset($argv[1]))
      die("index starting uri not specified!\n");
 
    set_time_limit(0);
    ini_set('memory_limit', '512M');
 
    //некие проектные установки, например include_path и проч.
    require_once(dirname(__FILE__) . '/../setup.php');
 
    require_once('limb/net/src/lmbUri.class.php');
    require_once('limb/web_spider/src/lmbWebSpider.class.php');
    require_once('limb/web_spider/src/lmbUriFilter.class.php');
    require_once('limb/web_spider/src/lmbContentTypeFilter.class.php');
    require_once('limb/web_spider/src/lmbSearchIndexingObserver.class.php');
    require_once('limb/search/src/indexer/lmbSearchTextNormalizer.class.php');
    require_once('limb/web_spider/src/lmbUriNormalizer.class.php');
    require_once('src/search/ZendSearchIndexer.class.php');
 
    $uri = new lmbUri($argv[1]);
 
    $indexer = new ZendSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->useNOINDEX();
 
    $observer = new lmbSearchIndexingObserver($indexer);
 
    $content_type_filter = new lmbContentTypeFilter();
    $content_type_filter->allowContentType('text/html');
 
    $uri_filter = new lmbUriFilter();
    $uri_filter->allowHost($uri->getHost());
    $uri_filter->allowProtocol('http');
    $uri_filter->allowPathRegex('~.*~');
 
    $normalizer = new lmbUriNormalizer();
    $normalizer->stripQueryItem('PHPSESSID');
 
    $spider = new lmbWebSpider();
    $spider->setContentTypeFilter($content_type_filter);
    $spider->setUriFilter($uri_filter);
    $spider->setUriNormalizer($normalizer);
    $spider->registerObserver($observer);
 
    $spider->crawl($uri);
    ?>

В приведенном выше скрипте происходит много чего интересного, однако, по-большому счету, мы просто объединяем несколько компонентов в единое целое и конфигурируем их. Более подробная информация о пакете WEB_SPIDER и SEARCH появится в соответсвующих разделах, здесь лишь приведем базовую информацию.

1. lmbWebSpider обходит веб контент по `<a href='..'>` ссылкам, которые он в нем находит. lmbWebSpider гарантирует, что ссылка будет посещена только однажды, т.е не произойдет зацикливания
2. lmbWebSpider позволяет настроить практически все аспекты своей работы при помощи объектов-стратегий, которые используются во время обхода контента: фильтрация ссылок(lmbUriFilter), нормализация ссылок(lmbUriNormalizer), фильтрация контента(lmbContentTypeFilter)
3. Каждый из компонентов, в свою, очередь также настраивается, например, для lmbUriFilter ставятся параметры привязки только к конкретному хосту(allowHost), использования только http протокола(allowProtocol) и использование любых путей(регулярное выражение ~.*~ в allowPathRegex). Методы, на мой взгляд, говорят о своем предназначении.
4. lmbWebSpider поддерживает интерфейс Observerable, т.е он позволяет зарегистрировать слушателей, которым посылается сообщение при обходе каждой новой страницы
5. Именно слушатель lmbSearchIndexingObserver делегирует работу по индексации контента индексатору ZendSearchIndexer

Использование из командной строки этого шелл скрипта крайне простое:

    $ php indexer.php http://mysite.com

При выполнении скрипт выводит информацию о том, какие именно страницы обходятся в данный момент. Если все нормально, должно появится нечто похожее на это:

    1)started indexing http://mysite.com...done
    3)started indexing http://mysite.com/news...done
    4)started indexing http://mysite.com/about...done
    5)started indexing http://mysite.com/en...done
    6)started indexing http://mysite.com/en/news...done
    ...

## Класс-индексатор
Приведем код индексатора:

**src/search/ZendSearchIndexer.class.php**

    require_once('Zend/Search/Lucene.php');
    require_once('src/search/SearchTextTools.class.php');
 
    class ZendSearchIndexer
    {
      protected $normalizer = null;
 
      protected $left_bound = '<!-- no index start -->';
      protected $right_bound = '<!-- no index end -->';
      protected $use_noindex = false;
 
      protected $index;
 
      function __construct($normalizer)
      {
        $this->normalizer = $normalizer;
      }
 
      function useNOINDEX($status = true)
      {
        $this->use_noindex = $status;
      }
 
      function index($uri, $content)
      {
        //получаем содержимое <title>...</title>
        $title = $this->_extractTitle($content);
        //вырезаем контент который необходимо необходимо индексировать,
        //если стоит опция use_noindex
        $content = $this->_getIndexedContent($content);
 
        //нормализуем контент нормализатором(такое вот масло масляное):
        //вырезаем теги
        $content = $this->normalizer->process($content);
 
        //индексируем контент
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('uri', $uri->toString()));
        //производим транслитерацию полей из русскоязычного UTF8 в us-ascii
        $field = Zend_Search_Lucene_Field::Text('title', SearchTextTools :: sanitize($title));
        //увеличиваем релевантность заголовков
        $field->boost = 1.5;
        $doc->addField($field);
        $doc->addField(Zend_Search_Lucene_Field::Text('content', SearchTextTools :: sanitize($content)));
        //сохраняем оригинальный заголовок без изменений, в поиске он не участвует!
        $doc->addField(Zend_Search_Lucene_Field::Binary('title_orig', $title));
 
        $index = $this->_getIndex();
 
        //индексер от ZendFramework выдает надоедливый notice во время использования iconv,
        //избавляемся от него, возможно, стоит просто на время индексации ставить error_level
        //без notice
        @$index->addDocument($doc);
      }
 
      function _getIndex()
      {
        if(!$this->index)
          $this->index = Zend_Search_Lucene::create(LIMB_VAR_DIR . '/search_index');
        return $this->index;
      }
 
      function _getIndexedContent($content)
      {
        if(!$this->use_noindex)
          return $content;
 
        $regex = '~' .
                 preg_quote($this->left_bound) .
                 '(.*?)' .
                 preg_quote($this->right_bound) .
                 '~s';
 
        return preg_replace($regex, ' ', $content);
      }
 
      function _extractTitle(&$content)
      {
        $regex = '~<title>([^<]*)</title>~';
        if(preg_match($regex, $content, $matches))
          return $matches[1];
        else
          return '';
      }
    }
    ?>

Центральным методом индексера является index($uri, $content), в который приходит объект lmbUri и ненормализованный контент(со всеми тегами). В комментариях написано, что именно происходит, остановимся лишь на непонятном аттрибуте $use_noindex и методе SearchTextTools :: sanitize(..):

1. Индексер позволяет вырезать из контента места, которые не следует индексировать. Например, у каждого сайта есть меню навигации, которое повторяется на каждой странице, индексировать его - совершенно лишнее. Так, если в меню навигации есть пункт «Новости», то при поиске «Новости» отобразятся все страницы! Чтобы не индексировать это меню или другой подобный повторяющийся контент, следует поместить его в дизайне HTML шаблона в пару маркеров: **<!– no index start –>…<!– no index end –>**. Таких маркеров можно иметь любое количество в шаблоне. Для активации подобной возможности необходимо вызвать метод useNOINDEX(..), что и происходит в шелл скрипте
2. SearchTextTools :: sanitize(..) производит транслитерацию и нормализацию контента, на выходе получается ascii строка в нижнем регистре(«ВаСилий» ⇒ «vasiliy»). Zend_Search как-то очень странно работает с utf-8 кодировкой, если ни сказать, что не работает вообще(iconv ругается и ничего не индексируется), хотя в документации и почтовой рассылке утверждается обратное. У нас не было особо времени разбираться, т.к требовалось рабочее решение для одного из проектов, поэтому мы поступили следующим образом: весь utf-8 русскоязычный контент мы транслитерируем в ascii и индексируем именно его. Причем, для того, чтобы выводить оригинальный заголовок, мы его сохраняем без изменений в бинарном неиндексируемом поле 'title_orig'. Как видно, это не лучшее решение, ограничено работой только с русскоязычным и англоязычным контентом, но его вполне хватает. Пока же будем ждать поддержки utf-8 в Zend_Search.

## Обработка и нормализация контента
Транслитерация и очистка текста от не ascii символов производится классом SearchTextTools:

**src/search/SearchTextTools.class.php**

    <?php
    class SearchTextTools
    {
      function translit($input)
      {
        $arrRus = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м',
                        'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь',
                        'ы', 'ъ', 'э', 'ю', 'я',
                        'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М',
                        'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь',
                        'Ы', 'Ъ', 'Э', 'Ю', 'Я');
        $arrEng = array('a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm',
                        'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '',
                        'y', '', 'e', 'ju', 'ja',
                        'A', 'B', 'V', 'G', 'D', 'E', 'JO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M',
                        'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'C', 'CH', 'SH', 'SCH', '',
                        'Y', '', 'E', 'JU', 'JA');
        return str_replace($arrRus, $arrEng, $input);
      }
 
      function sanitize($content)
      {
        $content = strtolower(self :: translit($content));
        $len = strlen($content);
        $res = "";
        for($i=0; $i < $len; ++$i)
        {
          $ord = ord($content{$i});
 
          if($ord >= 0x80)
            continue;
 
          $res .= $content{$i};
        }
        return $res;
      }
    }
    ?>

Здесь должно быть все понятно, поэтому перейдем сразу к контроллеру

## Котроллер, осуществляющий поиск
**src/controller/SearchController.class.php**

    <?php
    class SearchController extends lmbController
    {
      function doSearch()
      {
        require_once('src/search/SearchTextTools.class.php');
        require_once('Zend/Search/Lucene.php');
 
        $query = SearchTextTools :: sanitize(implode(' ', $this->_getQueryWords()));
        $index = Zend_Search_Lucene :: open(LIMB_VAR_DIR . '/search_index');
        try
        {
          $hits = $index->find($query);
        }
        catch(Zend_Exception $e)
        {
          $hits = array();
        }
 
        $result = array();
        foreach($hits as $hit)
        {
          $result[] = array('id' => $hit->id,
                            'score' => $hit->score,
                            'title' => $hit->title_orig,
                            'uri' => $hit->uri);
        }
 
        $this->view->set('search_result', $result);
      }
 
      protected function _getQueryWords()
      {
        $query = $this->request->get('query_string');
        return explode(' ', htmlspecialchars($query));
      }
    }
    ?>

В контроллере мы получаем строку запроса и обрабатываем ее, т.к индекс у нас хранится в транслите, следовательно и запрос поиска тоже необходимо делать в транслите, что и делается при помощи SearchTextTools :: sanitize(..). После этого собственно делаем запрос.

Помните мы хранили бинарную неиндексируемую строку заголовка без изменений? Именно ее мы и возвращаем во view вместо транслителируемой.

## Несколько финальных замечаний
Кроме проблем с индексацией не ascii строк, есть еще одна неприятная особенность — невозможность частичного поиска слова, хотя, возможно мы плохо смотрели документацию Zend_Search.
