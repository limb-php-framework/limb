# Формы и поля форм (Form tags)
[lmbMacroFormElementTag](./tags/form_tags/lmb_macro_form_element_tag.md) — базовый класс для всех элементов формы.

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
