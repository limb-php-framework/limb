# Обзор предустановленных средств для отображения
Название | Описание	| Расширение шаблона
---------|----------|-------------------
lmbMacroView | Использует macro шаблоны	| .phtml
lmbBlitzView	| Использует blitz шаблоны	| .bhtml
lmbDummyView	| Заглушка для тестов
lmbJsonView	| Отображение в JSON формате
lmbPHPView	| Использование чистого PHP в качестве шаблонов	| .php
lmbWactView |	Устаревший предшественник LIMB MACRO | .html

Некоторые средства отображения, такие как lmbDummyView и lmbJsonView работают без шаблонной подсистемы, по этому расширения для шаблонов опущены.

## Задание поддерживаемых средств отображения

    lmb_env_setor('LIMB_SUPPORTED_VIEW_TYPES', '.phtml=lmbMacroView;.html=lmbWactView');

## Ручное использование средства отображения

    //код
    $view = new lmbBlitzView(dirname(__FILE__).'/../../template/test/display.tpl');
    $view->set('foo', 'bar');
    return $view->render();
 
    //шаблон
    {{ $foo }}_baz
 
    //результат
    bar_baz
