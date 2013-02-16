# Вывод списков
## MACRO-теги для вывода списков
Для вывода списков в шаблонах используется группа тегов [для работы со списками](./tags.md).

     <table border="1">
       <tr>
        <th>№</th>
        <th>Название</th>
        <th>Объем, л</th>
        <th>Вес, кг</th>
       </tr>
     {{list using="$#tanks" as="$tank" counter="$number"}}
       {{list:item}}
       <tr>
        <td>{$number}</td>
        <td>{$tank.name}</td>
        <td>{$tank.volume}</td>
        <td>{$tank.weight}</td>
       </tr>
       {{/list:item}}
      {{list:empty}}
      <tr><td colspan='4'>Список пуст!</td></tr>
      {{/list:empty}}
     {{/list}}
    </table>

* [Тег {{list}}](./tags/list_tags/list_tag.md) — основной тег, который выводит свое содержимое, если переменная, указанная в атрибуте **using** содержит непустой список (массив или итератор). Атрибут **as** указывает на имя переменной, которая должна содержать очередной элемент списка. По-умолчанию, в качестве **as** используется значение $item.
* [Тег {{list:item}}](./tags/list_tags/list_item_tag.md) — повторяет определенную порцию шаблона по количеству элементов в итераторе тега {{list}}.
* [Тег {{list:empty}}](./tags/list_tags/list_empty_tag.md) — выводит свое содержимое только в том случае, если список не содержит ни одного элемента.
* [Тег {{list:glue}}](./tags/list_tags/list_glue_tag.md) — используется для разделения одного или группы элементов в списке.
* Переменная $number — содержит номер элемента в списке. Эта переменная генерится, так как мы указали атрибут **counter** для тега {{list}}.

В результате мы можем получить несколько результатов. Например, такой:

| № | Название        | Объем, л | Вес, кг |
|---|-----------------|----------|---------|
| 1 | Цистерна АБ-102 | 2400     | 340     |
| 2 | Цистерна АБ-103 | 2000     | 300     |

Или такой:

| № | Название        | Объем, л | Вес, кг |
|---|-----------------|----------|---------|
| Список пуст! |

## Вывод данных в несколько столбцов
Для вывода данных в несколько столбцов можно применять [тег {{list:glue}}](./tags/list_tags/list_glue_tag.md), который выводит определенный кусок кода раз в несколько элементов списка.

Например:

    {{list using="$#images"}}
    <table>
    <tr>
       {{list:item}}
       <td>
        <img src='{$item.path}' border='0' /><br />{$item.title}
       </td>
       {{list:glue step="3"}}</tr><tr>{{/list:glue}}
       {{/list:item}}
    </tr>
    </table>
    {{/list}}

Позволит вывести изображения в 3 колонки.

Если вы заметили, то выбранный нами способ для списков, например, с 10 элементами сгенерирует невалидную верстку, поэтому примеры необходимо немного доработать:

    {{list using="$#images"}}
    <table>
    <tr>
      {{list:item}}
        <td>
         <img src='{$item.path}' border='0' /><br />{$item.title}
        </td>
       {{list:glue step="3"}}</tr><tr>{{/list:glue}}
     {{/list:item}}
     {{list:fill upto='3' items_left='$items_left'}}
      <td colspan='{$items_left}'>&nbsp;</td>
     {{/list:fill}}
    </tr>
    </table>
    {{/list}}

Здесь мы использовали [тег {{list:fill}}](./tags/list_tags/list_fill_tag.md), который выполняет код шаблона, только если список не содержал количество элементов, кратное числу **upto**. При этом тег {{list:fill}} заполняет переменную с именем **items_left**.

В нашем случае мы просто предпочли вывести заглушку в виде <td colspan='2'>. Мы могли бы также воспользоваться [тегом {{repeat}}](./tags/core_tags/repeat_tag.md) для генерации недостающих ячеек:

    {{list using="$#images"}}
    <table>
    <tr>
     {{list:item}}
        <td>
         <img src='{$item.path}' border='0' /><br />{$item.title}
        </td>
       {{list:glue step="3"}}</tr><tr>{{/list:glue}}
     {{/list:item}}
     {{list:fill upto='3' items_left='$some_var'}}
       {{repeat times='{$some_var}'}}
       <td>
        <img src='/images/no_image.gif' alt='sorry, no image' />
       </td>
       {{repeat}}
     {{/list:fill}}
    </tr>
    </table>
    {{/list}}

## Дополнительные примеры
* см. примеры для [тега {{list}}](./tags/list_tags/list_tag.md)
* см. примеры для [тегa {{list:glue}}](./tags/list_tags/list_glue_tag.md)
