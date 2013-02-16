# Шаг 5. Отображение списка товаров для покупателей. Поиск товаров
Последовательность наших действий будет следующей:

* Мы создадим специальный статический метод Product :: **findForFront()**, который будет возвращать только список доступных товаров ($is_available == true). Именно эти товары мы будем отображать на фронтальной части.
* Мы доработаем контроллер **ProductController** и приведем в порядок базовый шаблон **product/display.html** для отображения списка товаров.
* Мы создадим **AlphabetFetcher**, который вернет список букв алфавита. В шаблоне product/display.html мы предоставим возможность покупателям просматривать товары, начинающиеся с какой-либо конкретной буквы. В методе **Product :: findForFront()** мы сделаем соответствующие изменения, чтобы он анализировал пришедшие ограничения.
* Мы создадим форму для поиска товаров, а в методе Product :: findForFront() добавим анализ дополнительных ограничивающих параметров. Для полноценной поддержки формы поиска мы также добавим немного кода в контроллер ProductController.

Этого будет вполне достаточно чтобы продемонстрировать один из подходов реализации вывода данных с различными ограничениями при помощи Limb.

## Первоначальный вывод товаров
### Изменения в классе Product
Во фронтальной части мы должны обеспечить следующее условие — отображению подлежат только «доступные» товары, то есть те, у которых стоит флаг is_available. Для этого мы создадим дополнительный статический метод findForFront():

    lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');
 
    class Product extends lmbActiveRecord
    {
      [...]
      static function findForFront()
      {
        $criteria = new lmbSQLRawCriteria('is_available = 1');
        return lmbActiveRecord :: find('Product', $criteria);
      }
    }

С классом [lmbCriteria](../../../../dbal/docs/ru/dbal/criteria.md) вы должны быть уже знакомы по реализации функционала по управлению пользователями. Здесь же мы использовали класс **lmbSQLRawCriteria** для вставки условия в запрос «как есть» (as is). Мы сразу решили использовать объектную форму критерии при вызове метода lmbActiveRecord :: find(), так как знаем, что нам придется в ближайшее время расширить этот функционал.

### Доработка ProductController
Применим новый метод для передачи данных в шаблон:

Файл shop/src/controller/ProductController.class.php:

    <?php
    lmb_require('limb/cms/src/controller/lmbObjectController.class.php');
    lmb_require('src/model/Product.class.php');
 
    class ProductController extends lmbObjectController
    {
      protected $_object_class_name = 'Product';
 
      function doDisplay()
      {
        $this->items = Product :: findForFront();
      }
    }

Мы могли бы использовать PullView и получать данные непосредственно в шаблоне, но не будем этого делать, т.к. пока не знаем всех параметров выборки.

### Шаблон product/display.phtml

Файл shop/template/product/display.phtml:

    <? $this->title ='Products'; ?>
    {{wrap with="front_page_layout.phtml" into="content_zone"}}
    {{include file='_admin/pager.phtml' items="$#items" per_page="5"/}}
    <br/>
    {{list using="$#items"}}
    <table cellpadding="0" cellspacing="0" class='list'>
      {{list:item}}
      <tr>
       <td>
          <dl>
            <dt>
              <b>{$item.title}</b><br />
              Price: <b>${$item.price|number:2, '.', ' '}</b><br/>
             </dt>
             <dd>
                <img src='{$item.image_path}' class='img'/>
                {$item.description|nl2br}
             </dd>
          </dl>
        </td>
      </tr>
      {{/list:item}}
    </table>
    {{/list}}
    {{/wrap}}

Получения данных в нашем случае происходит из переменной $this→items, которая заполняется методом findForFront(). Таким образом, во фронтальной части мы будем отображать только доступные товары.

Теперь можете попробовать зайти на страницу /product вашего приложения. Вы должны увидеть список товаров, разделенный на страницы (если элементов больше 5).

## Реализация ограничений по первой букве
Теперь мы приступим к реализации поиска товаров по первым буквам. Это потребует отображения списка букв в шаблоне, а также наложения ограничений на выборку товаров, название которых начинается с выбранной буквы.

Что нам для этого нужно:

* хранить где-то список букв
* хранить где-то текущую букву

Весь этот функционал мы соберем в одном месте — классе AlphabetHelper.

### Класс AlphabetHelper
Итак, список букв мы будем формировать при помощи класса AlphabetHelper. Если какая-либо буква является выбранной на данный момент, мы ее также помечаем определенным образом (метод isCurrent()).

Файл shop/src/helper/AlphabetHelper.class.php:

    <?php
 
    class AlphabetHelper
    {
      protected $_request_param_name = 'letter';
    	protected $_current_letter = '';
 
      function __construct()
      {
        $request = lmbToolkit :: instance()->getRequest();
        if($request->has($this->_request_param_name))
          $this->_current_letter = $request->get($this->_request_param_name);
      }
 
      function getAlphabet()
      {
        $result = array();
        for($i = 'A'; $i <= 'Z'; $i++)
        {
        	if(1 !== strlen($i))
        	  continue;
          $result[] = $i;
        }
        return $result;
      }
 
      function getCurrentLetter()
      {
        return $this->_current_letter;
      }
 
      function getRequestParamName()
      {
        return $this->_request_param_name;
      }
    }

Класс AlphabetFetcher отнаследован от класса lmbFetcher. **lmbFetcher** — это базовый класс, от которого наследуются все остальные fetcher-ы. Дочерние классы перекрывают метод _creataDataset(), из которого они должны обязательно возращать итератор.

### Инстанцирование хэлпера
Связующей точкой модели, шаблона и запроса является контроллер. В нем мы и будем инстанцировать наш хэлпер.

Файл /shop/src/controller/ProductController.class.php:

    <?php
    lmb_require('limb/cms/src/controller/lmbObjectController.class.php');
    lmb_require('src/model/Product.class.php');
    lmb_require('src/helper/AlphabetHelper.class.php');
 
    class ProductController extends lmbObjectController
    {
      protected $_object_class_name = 'Product';
 
      function doDisplay()
      {
        $this->helper = new AlphabetHelper();
        $this->items = Product :: findForFront($this->helper->getCurrentLetter());
      }
    }

### Изменения в шаблоне product/display.phtml
Теперь мы будем использовать новый AlphabetHelper в шаблоне для вывода списка букв. Для этого нам необходимо немного модифицировать шаблон product/display.phtml.

Файл shop/template/product/display.phtml:

    [...]
    <a href="{{route_url params='controller:product'}}">Display all</a>
    {{list using="$#helper->getAlphabet()"}}
        {{list:item}}
        <? if ($this->helper->getCurrentLetter() == $item) { ?>
        <b>{$item|uppercase}</b>
        <? } else { ?>
        <?php $letter_param = $this->helper->getRequestParamName(); ?>
        <a href='/product?{$letter_param}={$item}'>{$item|uppercase}</a>
        <? } ?>
        {{/list:item}}
    {{/list}}
    [...]

### Изменения в классе Product для учитывания ограничений
Теперь нам необходимо, что метод Product :: findForFront() учитывал ограничения по первой букве названия товара, если необходимо.

Файл shop/src/model/Product.class.php:

    <?php
    class Product extends lmbActiveRecord
    {
      [...]
      static function findForFront($title_begin = null)
      {
        $criteria = lmbSQLCriteria::equal('is_available', 1);
      	if ($title_begin)
      	  $criteria->addAnd(lmbSQLCriteria :: like('title', $title_begin.'%'));
      	return Product :: find($criteria);
      }
    [...]

Метод Product :: findForFront($title_begin) добавляет в $criteria дополнительное условие, если в него передают значение. Мы использовали метод addAnd() для добавления дополнительного условия. Обратите внимание, что знаки % для LIKE условия необходимо расставлять самостоятельно.

Сейчас можете попробовать этот новый функционал. Вы должны увидеть список букв и при нажатии на какую-либо из них, выборка товаров будет соответствующим образом ограничена:

![Alt-alphabet](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:alphabet.png)

## Реализация формы поиска товаров
### Добавление формы поиска в шаблон product/display.phtml
Добавим новую форму для поиска товаров. Поиск будет доступен по названию продукта, а также по цене (больше какого-либо значения и(или) меньше).

Модификация файла shop/template/product/display.phtml:

    [...]
 
    <p><strong>Search the products:</strong></p>
    {{form method="GET" id='search_form' name='search_form' action='product'}}
      <label for='title'>Product title:</label>
      {{input type="text" name="title" id="product" size='10'/}}
 
      <label for='price_greater'>Price greater:</label>
      {{input type="text" name="price_greater" id="price_greater" type="text" size='4'/}}
 
      <label for='price_less'>Price less:</label>
      {{input type="text" name="price_less" id="price_less" type="text" size='4'/}}
 
      <input type='submit' name='search' value="Search!" class='button'/><br/>
    {{/form}}
    [...]

### Модификация контроллера ProductController
Теперь нам необходимо модифицировать ProductController и Product, чтобы они принимали новые параметры фильтрации.

Файл shop/src/controller/ProductController.class.php:

    [...]
      function doDisplay()
      {
        $this->helper = new AlphabetHelper();
      	$this->useForm('search_form');
        $this->setFormDatasource($this->request);
 
        $this->items = Product :: findForFront($this->_getSearchParams());
      }
 
      function _getSearchParams()
      {
      	$params = array();
 
      	if($this->request->get('title'))
      	  $params['title'] = $this->request->getSafe('title');
 
      	if($this->request->get('price_greater'))
          $params['price_greater'] = $this->request->getInteger('price_greater');
 
        if($this->request->get('price_less'))
          $params['price_less'] = $this->request->getInteger('price_less');
 
        return $params;
      }
    [...]
    
Как вы видите мы получаем из запроса параметры title, price_great, price_less, и передаем их в Product :: findForFront($params), предварительно отфильтровав. Мы могли бы передавать туда сам объект запроса (request), но это стало бы смешением уровней приложения.

### Изменения в классе Product для учета новых критерий
Теперь можно модифицировать метод Product :: findForFront() для того, чтобы он учитывал данные, пришедшие с формы поиска.

Файл shop/src/model/Product.class.php:

    [...]
      static function findForFront($params = array())
      {
        $criteria = lmbSQLCriteria::equal('is_available', 1);
 
      	if (isset($params['title']))
      	  $criteria->addAnd(lmbSQLCriteria :: like('title', $params['title'].'%'));
 
      	if (isset($params['price_greater']))
          $criteria->addAnd(lmbSQLCriteria :: greater('price', (int) $params['price_greater']));
 
        if (isset($params['price_less']))
          $criteria->addAnd(lmbSQLCriteria :: less('price', (int) $params['price_less']));
 
      	return Product :: find($criteria);
      }
    [...]

Поиск по первой букве является по сути частным случаем поиска по названию. Поэтому мы можем изменить значение перемнной $_request_param_name класса AlphabetHelper на 'title', и нам не придется делать еще один передаваемый параметр.

Надеемся, что наши последние действия в комментариях не требуются, и все понятно.

## Предварительные итоги
Небольшой скриншот того, как должен выглядеть список товаров при использовании формы поиска:

![Alt-search_products](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:search_products.png)

## Далее
У нас уже есть список доступных к продаже товаров, теперь настало время добавить потенциальным покупателям добавлять приглянувшиеся им товары к корзину и оформлять заказ.

Итак, следующий шаг: [Шаг 6. Работа покупателей с корзиной заказа](./step6.md)
