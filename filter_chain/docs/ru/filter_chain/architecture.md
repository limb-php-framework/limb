# Архитектура пакета FILTER_CHAIN
## UML диаграммы
### Статическая диаграмма классов
![Alt-Статическая диаграмма классов](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:filter_chain:limb_3.x_filter_chain.png)

То есть по сути пакет состоит из класса **lmbFilterChain** и интерфейса **lmbInterceptingFilter**.

### Диаграмма последовательностей
![Alt-Диаграмма последовательностей](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:filter_chain:limb_3.x_sequence.png)

## Составление главной цепочки фильтров приложения
Использование фильтров в качестве, например, FrontController позволяет сконцентрировать управление логикой приложения в одном месте. Зачастую ваше приложение будет по сути представлять из себя обычную цепочку фильтров, а его работа будет заключаться в проходе по этой цепочке фильтров. Ваш index.php файл будет выглядеть приблизительно так:

    $chain = new lmbFilterChain();
    $chain->registerFilter(new lmbHandle('path_to_filter_1'));
    $chain->registerFilter(new lmbHandle('path_to_filter_2'));
    $chain->process(); 

Также можно обратить внимание на то, что фильтры регистрируются как хендлы, которые становятся реальными объектами только тогда, когда до них доходит очередь. Подробнее о хендлах в разделе [lmbHandle](../../../core/docs/ru/core/handles.md). Это позволяет экономить на парсинге кода, если очередь до определенных фильтров так и не дойдет, например, при полностраничном кешировании.

Конечная конфигурация вашей цепочки сильно зависит от того, насколько сложное приложение вы делаете.

Сама цепочка фильтров похожа на «матрешку»:

    +-filter1
    |
    | +-filter2
    | | 
    | | +-filter3
    | | |
    | | |_
    | | 
    | |_
    |
    |_

Каждый фильтр самостоятельно решает, передавать ли контроль следующему фильтру или нет. Например, тело некоторого фильтра может выглядеть приблизительно так:

    lmb_require(LPKG_FILTER_CHAIN_DIR . '/src/lmbInterceptingFilter.interface.php');
 
    class SimpleInterceptingFilter implements lmbInterceptingFilter
    {       
      function run($filter_chain)
      { 
        //pre processing is done here
 
        if ($this->someConditionPassed())
          $filter_chain->next();    
 
        //post processing is done here
      }      
    }
