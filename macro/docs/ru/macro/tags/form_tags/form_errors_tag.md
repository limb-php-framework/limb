# Теги {{form:errors}} и {{form:field_errors}}
## Тэг {{form:errors}}
### Описание
Передает список ошибок с формы в переменную, которая обычно выводится при помощи [{{list}}](../list_tags/list_tag.md)-тегов. Ошибка валидации формы — это обычный итератор (массив), где каждый элемент имеет поле {$message}.

### Синтаксис

    {{form:errors to='$some_var' /}}

### Область применения
Внутри тега [{{form}}](./form_tag.md).

### Атрибуты
**to** — название переменной, которая получит список ошибок.

### Содержимое
Нет.

### Пример использования

    {{form id="simpleform" method="post"}}
 
    {{form:errors to='$fields_errors'/}}
 
    {{list using='$fields_errors' as="$error"}}
    <ol>
      {{list:item}}
        <li><span style="color:red">{$error.message}</span></li>
      {{/list:item}}
    </ol>
    {{/list}}
 
    [... поля формы ...]
 
    {{/form}}

В результате будет выведен список ошибок для всей формы.

## Тэг {{form:field_errors}}
### Описание
Аналогичен **{{form:errors}}**, с той лишь разницей, что предназначен для отдельного поля.

### Синтаксис

    {{form:field_errors to='$some_var' for='some_field'/}}

### Область применения
Внутри тега [{{form}}](./form_tag.md).

### Атрибуты
* **to** — название переменной, которая получит список ошибок.
* **for** — название поля, ошибки которого надо получить.

### Содержимое
Нет.

### Пример использования

    {{form id="simpleform" method="post"}}
 
    {{input type="text" name="name" id="name" title="Имя и фамилия"/}}
 
    {{form:field_errors to='$name_errors' for="name"/}}
    {{list using='$name_errors' as="$error"}}
    <ol>
      {{list:item}}
        <li><span style="color:red">{$error.message}</span></li>
      {{/list:item}}
    </ol>
    {{/list}}
 
    {{/form}}

В результате будет выведен список ошибок для всей формы.
