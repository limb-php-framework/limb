# Как работает компилятор MACRO-шаблонов
## Цель этой страницы
Эта страница будет полезной для разработчиков, которым необходимо создать свои собственные теги или фильтры. Она позволит понять, каким образом MACRO компилирует шаблоны. Мы подробно разберем процесс компиляции шаблонов, укажем на основные классы, участвующие в компиляции, покажем, как генерится php-код компилированных шаблонов, как отрабатывают выражения для вывода данных и т.д.

Очень глубоких деталей реализации MACRO-компилятора мы все же касаться не будем, но полученных знаний будет достаточно чтобы создать новый тег или фильтр.

Мы предполагаем, что Вам знаком материал раздела [«Компиляция и выполнение шаблона. Пример рендеринга MACRO-шаблона»](./important_details.md).

## Фаза компиляции и фаза исполнения
Вы уже знаете, что MACRO обрабатывает шаблоны в две стадии:

* **фаза компиляции (CompileTime)** и
* **фаза исполнения (RunTime)**.

На этапе компиляции шаблон собирается целиком и переводится в php-скрипт с уникальным классом, который содержит метод публичный **render()**, закрытый метод **_init()**, а также прочие методы, количество и имена которых зависят от использования тегов {{insert}}, {{insert:into}}, {{template}} и {{apply}}.

При компиляции шаблон разбирается и создается его структура в виде дерева. Каждый элемент этого дерева - это объект класса lmbMacroNode (или его дочерние). После создания дерева компиляции, компилятор проходит по этому дереву и для каждого элемента дерева вызывает метод generate($code_writer), тем самым просит каждый из компонентов фазы компиляции добавить что-либо в компилируемый шаблон. Объект $code_writer класс lmbMacroCodeWriter аккумулирует весь генерируемый код, чтобы затем его записать в файл.

Во время фазы исполнения (RunTime) запускается сначала метод **_init()** (аналог превдо-конструктора). Содержимое этого метода обычно используется для инициализации так называемых runtime компонентов, или просто helper-ов, которые используются для работы тех или иных тегов. К числу тегов, которые создают runtime-компоненты относятся, например, тег {pager}} и теги группы FormTags.

После выполнения метода **_init()** выполняется остальная часть метода **render()**, которая содержит смесь обычного html-кода с php-кодом.

Мы подробно опишем, как происходит компиляция на примере тега [{{paginate}}](./tags/pager_tags/paginate_tag.md).

## Подробно о процессе компиляции шаблонов
### Пример шаблона
Рассмотрим простейший пример вывода на экран произвольного списка заголовков:

    <html>
    <body>
    <h1>List example</h1>
 
    {{paginate iterator='$#items' pager='my_pager'}}
 
    {{pager id="my_pager" items="5"}}
    {{pager:prev}}<a href="{$href}">Prev page</a>{{pager:prev}}
    {{pager:next}}<a href="{$href}">Next page</a>{{pager:next}}
    {{/pager}}
 
    {{list using="$#items"}}
      <table border="0">
      {{list:item}}
      <tr>
        <td>{$item.title}</td>
      </tr>
      {{/list:item}}
      </table>
    {{/list}}
    </body>
    </html>

Теперь мы подробно покажем как MACRO откомпилирует данный шаблон.

Весь процесс компиляции управляется из класса **lmbMacroCompiler** (limb/macro/src/compiler/lmbMacroCompiler.class.php).

### Формирование дерева этапа компиляции
Во время компиляции шаблон анализируется и создается так называемое дерево фазы компиляции.

Это дерево состоит из элементов — **нодов**. Базовый класс для всех нодов дерева компиляции — **lmbMacroNode**, limb/macro/src/compiler/lmbMacroNode.class.php).

В это дерево в качестве элементов попадают практически все элементы шаблона:

* Обычный текст и HTML-код в виде **lmbMacroTextNode** (limb/macro/src/compiler/lmbMacroTextNode.class.php)
* PHP-код также виде **lmbMacroTextNode**
* MACRO-теги различных типов. Базовый класс для тега — **lmbMacroTag** (limb/macro/src/compiler/lmbMacroTag.class.php)
* Выражения вида {$title} вместе с фильтрами в виде объектов класса **lmbMacroOutputExpressionNode** (limb/macro/src/compiler/lmbMacroOutputExpressionNode.class.php)

В нашем примере мы получим следующее дерево:

    Root
     |
     |-lmbMacroTextNode
     |
     |-lmbMacroPaginateTag
     |
     |-lmbMacroPagerTag
     |     |
     |     |-lmbMacroPagerPrevTag
     |     |   |
     |     |   |-lmbMacroTextNode
     |     |
     |     |-lmbMacroPagerNextTag
     |         |
     |         |-lmbMacroTextNode
     |
     |-lmbMacroListTag
     |     |
     |     |-lmbMacroTextNode
     |     |
     |     |-lmbMacroListItemTag
     |     |   |
     |     |   |-lmbMacroTextNode
     |     |   |
     |     |   |-lmbMacroOutputExpressionNode
     |     |   |
     |     |   |-lmbMacroTextNode
     |     |
     |     |-lmbMacroTextNode
     |
     |-lmbMacroTextNode

### Класс lmbMacroNode
Класс lmbMacroNode — это основной класс ноды дерева фазы компиляции.

Каждый объект класса lmbMacroNode содержит следующие атрибуты:

* **children** — массив дочерних элементов
* **parent** — ссылку на родительский элемент.
* **node_id** — идентификатор элемента (генерится автоматически, если это необходимо).
* **location_in_template** — объект класса lmbMacroSourceLocation, указывающий точное расположение данного элемента в компилируемых шаблонах.

lmbMacroNode содержит необходимые методы для навигации по дереву и поиску необходимых элементов:

* **findChild($node_id)** — найти дочерний элемент по идентификатору. Поиск производится вниз по иерархии по всему дереву.
* **findUpChild($node_id)** — найти элемент вверх по иерархии. Область поиска начинается с дочерних элементов текущего элемента и расширяется по мере продвижения вверх по иерархии, так как вызывает метод findChild().
* **findChildByClass($class)** — аналогично findChild(), но ищет элементы по классу.
* **findChildrenByClass($class)** — аналогично findChildByClass, но возвращает массив элементов указанного класса.
* **findImmediateChildByClass($class)** — найти дочерний элемент (первый, если их несколько), принадлежащий именно данному элементу, по названию класса.
* **findImmediateChildrenByClass($class) — найти дочерние элементы, принадлежащий именно данному элементу, по названию класса.
* **findRoot()** — возвращает корненую ноду дерева
* **getChildren()** — возвращает массив дочерних элементов.
* **getParent()** — возвращает ссылку на родительский элемент.

Класс lmbMacroNode также содержит метод, который участвуют в процессе компиляции:

* **generate($code_writer)** — самый общий метод, в котором элементы дерева компиляции выполняют генерацию php-кода откомпиленного шаблона. Обычно дочерние классы редко перекрывают этот метод.

Параметр **$code_writer** — это объект класс lmbMacroCodeWriter, который содержит код откомпилированного шаблона.

### Класс lmbMacroCodeWriter
Параметр $code_writer методов генерации класса lmbMacroCodeWriter — это объект класса **lmbMacroCodeWriter** (limb/macro/src/compiler/lmbMacroCodeWriter .class.php).

Класс lmbMacroCodeWriter содержит следующие часто используемые методы:

* **writePHP($php)** — добавить php-код
* **writePHPLiteral($php)** — добавить php-код c escaping-ом.
* **writeHTML($text)** — добавить html-код или простой текст
* **registerInclude($include_file)** — добавить путь до файла, который необходимо будет подключить до инициализации откомпилированного шаблона.
* **beginMethod($name, $param_list = array())** — генерит начало метода. Весь последующий код будет добавляться в эту функцию.
* **endMethod()** — завершает генерацию метода класса. Курсор возвращается в предыдущий метод.
* **writeToInit($php_code)** — добавляет php-код в метод _init(), который вызывается сразу же при начале выполнения шаблона.
* **generateVar()** — генерит уникальное имя переменной с символом $ впереди. Получается что-то типа $A0001 (или короче).

Класс lmbMacroCodeWriter автоматически переключает контекст с html на php. Поэтому вы можете легко смешивать вызовы writeHTML() и writePHP().

### Класс lmbMacroTag
Класс lmbMacroTag — это основной класс для тегов. Он наследуется от lmbMacroNode , но добавляет функционал по работе с атрибутами тегов.

Вот список наиболее значительных методов для работы с атрибутами:

* **get($name)** — возвращает значение атрибута.
* **getEscaped($name)** — возвращает значение атрибута, однако escap-ит строки, если значение тега - это строка, а не переменная. Этот метод используется, если необходимо значение тега записать в компилируемый шаблон, однако неизвестно заранее, какое значение получит атрибут: строку или переменную.
* **has($attrib)** — возвращает true, если тег имеет соответствующий атрибут.
* **getBool($attrib, $default = FALSE)** — возвращает boolean-значение атрибута. Возвращает false, если атрибут имеет значение FALSE, F, N, No, NONE, 0.
* **remove($attrib)** — удаляет атрибут.
* **set($name, $value)** — добавляет атрибут.

Кроме этого класс lmbMacroTag содержит набор методов, которые его наследники используют при генерации:

* **_generateBeforeContent($code_writer)** — используется для генерации кода до содержимого тега.
* **_generateContent($code_writer)** — используется для генерации содержимого тега. По-умолчанию реализован таким образом, что передает управление на генерацию контента дочерних элементов.
* **_generateAfterContent($code_writer)** — используется для генерации кода после содержимого тега.

Обратите внимание, что перекрывать метод **generate($code)** наследникам lmbMacroTag крайне не рекомендуется, так как реализация lmbMacroTag :: generate($code) содержит важный код, который необходим для работы со сложными атрибутами (так называемая прегенерация атрибутов).

### Генерация php-кода MACRO-тегами
Рассмотрим тег [{{paginate}}](./tags/pager_tags/paginate_tag.md) и на его примере продемонстрируем, каким образом формируется компилированный шаблон.

Код класса lmbMacroPaginateTag можно найти в файле limb/macro/src/tags/pager/paginate.tag.php:

    <?php
    /**
     * Applies pager to iterator (so called "pagination")
     * @tag paginate
     * @req_attributes iterator
     * @package macro
     * @version $Id$
     */
    class lmbMacroPaginateTag extends lmbMacroTag
    {
      protected function _generateContent($code)
      {
        $iterator = $this->get('iterator');
 
        if($this->has('pager'))
        {
          if(!$pager_tag = $this->parent->findUpChild($this->get('pager')))
            $this->raise('Can\'t find pager by "pager" attribute in {{paginate}} tag');
 
          $pager = $pager_tag->getRuntimeVar();
 
          if($this->has('limit'))
            $code->writePhp("{$pager}->setItemsPerPage({$this->get('limit')});\n");
 
          $code->writePhp("{$pager}->setTotalItems({$iterator}->count());\n");
          $code->writePhp("{$pager}->prepare();\n");
          $offset = $code->generateVar();
          $code->writePhp("{$offset} = {$pager}->getCurrentPageBeginItem();\n");
          $code->writePhp("if({$offset} > 0) {$offset} = {$offset} - 1;\n");
          $code->writePhp("{$iterator}->paginate({$offset}, {$pager}->getItemsPerPage());\n");
          return;
        }
        elseif($this->has('offset'))
        {
          if(!$this->has('limit'))
            $this->raise('"limit" attribute for {{paginate}} is required if "offset" is given');
 
          $code->writePhp("{$iterator}->paginate({$this->get('offset')},{$this->get('limit')});\n");
          return;
        }
        elseif($this->has('limit'))
        {
          $code->writePhp("{$iterator}->paginate(0,{$this->get('limit')});\n");
          return;
        }
      }
    } 

Разберем подробно код класса.

Итак, lmbMacroPaginateTag является потомком от lmbMacroTag, то есть обычным MACRO-тегом.

Генерация кода обычно производится в методе **_generateContent($code_writer)** (об этом мы уже указывали в описании класса lmbMacroTag).

Рассмотрим следующие строки класса lmbMacroPaginateTag :

    if($this->has('pager'))
    {
      if(!$pager_tag = $this->parent->findUpChild($this->get('pager')))
        $this->raise('Can\'t find pager by "pager" attribute in {{paginate}} tag');
 
      $pager = $pager_tag->getRuntimeVar();
     [..]

Если тег содержит атрибут **pager**, тогда он пытается найти соответствующий элемент дерева при помощи метода findUpChild(), вызывая его у своего родителя. Метод **raise($message)** используется для генерации исключения, если во время компиляции произошла ошибка. Метод raise() автоматически добавляет в исключение информацию о том, где в шаблоне произошла ошибка.

Далее

    $pager = $pager_tag->getRuntimeVar();
 
    if($this->has('limit'))
      $code->writePhp("{$pager}->setItemsPerPage({$this->get('limit')});\n");

Первая строка этого блока возвращает имя переменной, по которой runtime компонент пейджера будет доступен в шаблоне на этапе выполнения.

Так как limit — это скорее всего число или переменная, а не строка, при генерации ее значения в шаблон мы использовали метод get($attr_name). В припципе, правильнее было бы использовать метод getEscaped($attr_name).

    $code->writePhp("{$pager}->setTotalItems({$iterator}->count());\n");
    $code->writePhp("{$pager}->prepare();\n");
    $offset = $code->generateVar();
    $code->writePhp("{$offset} = {$pager}->getCurrentPageBeginItem();\n");
    $code->writePhp("if({$offset} > 0) {$offset} = {$offset} - 1;\n");
    $code->writePhp("{$iterator}->paginate({$offset}, {$pager}->getItemsPerPage());\n");
    return;

Здесь мы также использовали генерацию временной переменной $offset при помощи метода lmbMacroCodeWriter :: generateVar();

В результате в компилированном шаблоне мы получим приблизительно следующий код, который сгенерит тег [paginate](./tags/pager_tags/paginate_tag.md):

### Генерация php-кода другими элементами шаблона
Теперь кратко покажем, как генерят компилированный шаблон остальные элементы дерева компиляции.

#### Обычный текст
Все, что не является MACRO-тегами или выражения, MACRO интерпретирует как обычный текст и выдает его в компилированный шаблон “как есть”, то есть внутри класса lmbMacroTextNode есть такой код:

      function generate($code_writer)
      {
        $code_writer->writeHTML($this->contents);
 
        parent :: generate($code_writer);
      }
    }
    ?>

где $this→contents и есть сам текст.

#### Выражения и фильтры
По-умолчанию выражения (если они не являются атрибутами MACRO-тегов) формируют в компилированном шаблоне код <?php echo ...; ?>. Остальное зависит от фильтров и от сложности самого выражения. Например, выражение {$title|html} фактически означает применение фильтра html к данным, а $title — это переменная. В результате из {$title|html} в откомпилированном шаблоне получим следующее:

    <?php echo htmlspecialchars($title, ENT_QUOTES); ?>
