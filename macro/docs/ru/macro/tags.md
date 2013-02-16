# Теги шаблонной системы MACRO
*Большинство тегов имеет примеры использования. Если целая группа тегов содержит только один пример, то страница этого тега указана особо: «Пример использования здесь!»*

## Базовые теги (Core Tags)
### Управление шаблонами
Тег | Описание
----|---------
| |Расположение: **limb/macro/src/tags/core/**
[include](./tags/core_tags/include_tag.md) | Вставляет содержимое другого шаблона или иного файла в текущий шаблон.
[slot](./tags/core_tags/slot_tag.md) | Представляет из себя место в шаблоне куда может быть вставлен другой шаблон или часть другого шаблона.
[wrap](./tags/core_tags/wrap_tag.md) | Вставляет часть шаблона в определенное место другого шаблона. Делает из этих двух шаблонов один — единый шаблон. В случае использования совместо с тегами [{{into}}](./tags/core_tags/into_tag.md) просто указывает на имя шаблона, куда будет вставляться содержимое тегов [{{into}}](./tags/core_tags/into_tag.md).
[template](./tags/core_tags/template_tag.md) | Определяет часть шаблона, которая может быть многократно использована при помощи тега [{{apply}}](./tags/core_tags/apply_tag.md). Комбинация [{{template}}](./tags/core_tags/template_tag.md) и [{{apply}}](./tags/core_tags/apply_tag.md) позволяет делать аналог [{{include}}](./tags/core_tags/include_tag.md), но только в рамках одного шаблона. Тег [{{template}}](./tags/core_tags/template_tag.md) без применения тега [{{apply}}](./tags/core_tags/apply_tag.md) не генерирует код.

### Прочие
Тег | Описание
----|---------
| |Расположение: **limb/macro/src/tags/core/**
[apply](./tags/core_tags/apply_tag.md) | Вставляет часть шаблона, указанное в рамках какого-то тега [{{template}}](./tags/core_tags/template_tag.md). По сути это тот же [{{include}}](./tags/core_tags/include_tag.md), только включаемый шаблон находится в текущем шаблоне
[into](./tags/core_tags/into_tag.md) | Вставляет часть шаблона в определенное место другого шаблона, отмеченное тегом [{{slot}}](./tags/core_tags/slot_tag.md) Используется внутри тега [{{wrap}}](./tags/core_tags/wrap_tag.md)
[copy](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroCopyAndCutTagsTest.class.php#L12) | Копирует содержимое в переменную-буффер
[cut](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroCopyAndCutTagsTest.class.php#L21)  | Перемещает содержимое в переменную-буффер
[nospace, trim, space, tab](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroNospaceTagTest.class.php#L11)  | Позволяют вырезать пробельные символы сохраняя наглядность разметки
[repeat](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroRepeatTagTest.class.php#L1) | Повторяет свое содержимое, указанное количество раз
[assign](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroAssignTagTest.class.php#L1) | Копирует содержимое одной переменной в другую
[Тэги if, elseif, else](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroIfAndElseIfAndElseTagsTest.class.php#L1)	| Ветвление тэгами

## Списки (List Tags)
Тег | Описание
----|---------
| |Расположение: **limb/macro/src/tags/list/**
[list](./tags/list_tags/list_tag.md) | Основной тег для вывода списков. **Пример использования здесь!**	
[list:item](./tags/list_tags/list_item_tag.md) | Повторяет определенный кусок шаблона по количеству элементов в переменной (размер массива или количество элементов в итераторе), указанной для {{list}} тега	
[list:empty](./tags/list_tags/list_empty_tag.md) | Отображает свое содержимое, если список пуст	
[list:glue](./tags/list_tags/list_glue_tag.md)	| Используется для разделения строк списка друг от друга	
[list:fill](./tags/list_tags/list_fill_tag.md)	| Используется, если количество элементов в списке некратное какому-то числу. Используется, например, для генерации валидной табличной верстки.

## Деревья (Tree Tags)
Тег | Описание
----|---------
| |Расположение: **limb/macro/src/tags/tree/**
[tree](./tags/tree_tags/tree_tag.md) | Основной тег для вывода деревьев. **Пример использования здесь!**	
[tree:node](./tags/tree_tags/tree_node_tag.md)	| Шаблон узла дерева для {{list}} тега	
[tree:nextlevel](./tags/tree_tags/tree_nextlevel_tag.md) | На место данного тэга вставляется html дочерних узлов	
[tree:empty](./tags/tree_tags/tree_empty_tag.md)  | Отображает свое содержимое, если в дереве нет узлов

## Формы и поля форм (Form tags) 
Тег | Описание
----|---------
| |Расположение: **limb/macro/src/tags/form/**
[form](./tags/form_tags/form_tag.md) | Расширенная форма html-тега `<form>`  
[form:errors](./tags/form_tags/form_errors_tag.md) | Используется для отображения списка ошибок валидации формы.  
[form:field_errors](./tags/form_tags/form_errors_tag.md) | Используется для управления списком ошибок валидации формы.	
[form:referer](./tags/form_tags/form_referer.md) | Добавляет скрытое поле (`<input type=«hidden»>`) URL источника запроса	
[input](./tags/form_tags/input_tag.md)	| Расширеная форма html-тега `<input>`.	
[textarea](./tags/form_tags/text_area_tag.md) | Расширенная форма html-тега `<textarea>`.	
[label](./tags/form_tags/label_tag.md)	| Расширенная форма тега `<label>`.	
[js_checkbox](./tags/form_tags/js_checkbox_tag.md) | Вставляет DHML checkbox, а также hidden поле, которое всегда возвращает какое-либо значение ('0' или '1').	
[select](./tags/form_tags/select_tag.md) | Расширенная форма html-тега `<select>`.	
[option](./tags/form_tags/option_tag.md) | Тег опции выбора для тега {{select}}.	
[select_options_export](./tags/form_tags/select_options_export_tag.md)	| Позволяет преобразовывать контейнеры данных в виде итераторов или обычные массивы в такую форму, которая понятна тегу {{select}} для построения списка тегов <option>.

## Пейджинация (теги для разбиения большого набора данных на страницы)
Тег | Описание
----|---------
| |Расположение: **limb/macro/src/tags/pager/**
[pager](./tags/pager_tags/pager_tag.md) | Основной тег для лимитирования итератора и вывода списка страниц. **Пример использования здесь!**	
[paginate](./tags/pager_tags/paginate_tag.md) | Позволяет связывать pager со списком прямо в шаблоне.	
[Теги pager:prev, pager:first, pager:next, pager:last](./tags/pager_tags/pager_frontier_tag.md) | |Позволяет вывести ссылку на предыдущую, первую, следующую и последнуюю страницы списка страниц.	
[Теги pager:prev:disabled, pager:first:disabled, pager:next:disabled, pager:last:disabled](./tags/pager_tags/pager_disabled_frontier_tag.md) | Позволяет вывести какой-либо текст если переход на соответствующую страницу невозможен. **Пример использования здесь!**	
[pager:list](./tags/pager_tags/pager_list_tag.md) | Выводит список страниц.	
[pager:number](./tags/pager_tags/pager_number_tag.md) | Позволяет вывести ссылку на страницу списка страниц.	
[pager:current](./tags/pager_tags/pager_current_tag.md)	| Позволяет вывести ссылку на текущую страницу	
[pager:separator](./tags/pager_tags/pager_separator_tag.md)	| Выводит разделитель между страницами списка страниц.	
[pager:section](./tags/pager_tags/pager_section_tag.md)	| Позволяет вывести ссылку на группу страниц списка страниц.	
[pager:elipses](./tags/pager_tags/pager_elipses_tag.md)	| Позволяет вывести разделитель между группами ссылок на страницы в начале списка страниц, в середине и в конце pager-а.

## Ссылки
Тег | Описание
----|---------
| |Расположение: **limb/web_app/src/macro/**
[route_url](./tags/lmb_request_tags/lmb_route_url_tag.md) | Формирует и выводит путь (URL), составленный при помощи при помощи класса [lmbRoutes](../../../../web_app/docs/ru/web_app/lmb_routes.md).

## Интернационализация (пакет I18N)
Тег | Описание
----|---------
| |Расположение: **limb/i18n/src/macro/**
[i18n](./tags/i18n_tags/lmbi18n_tag.md) | Переводит строку текста при помощи функции lmb_i18n.

## Календарь (пакет CALENDAR)
Тег | Описание
----|---------
| |Расположение: **limb/calendar/src/macro/**
[date3select](./tags/calendar_tags/date3select.md) | Выводит 3 выпадающих списка
[datetime](./tags/calendar_tags/datetime.md) | Выводит JavaScript-календарь

## WYSIWYG (пакет WYSIWYG)
Тег | Описание
----|---------
| |Расположение: **limb/wysiwyg/src/macro/**
[wysiwyg](./tags/wysiwyg_tags/lmb_wysiwyg_tag.md) | Отображает wysiwyg-редактор

## Кеширование (пакет CACHE2)
Тег | Описание
----|---------
| |Расположение: **limb/cache2/src/macro/**
[cache](./tags/cache2_tags/cache_tag.md) | Частичное кеширование страницы

## Права доступа (пакет ACL)
Тег | Описание
----|---------
| |Расположение: **limb/acl/src/macro/**
[allowed](./tags/acl_tags/allowed_tag.md) | Проверка доступа
