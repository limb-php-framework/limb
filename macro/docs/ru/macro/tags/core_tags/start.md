# Базовые теги (Core Tags)
## Управление шаблонами
Тег | Описание
----|---------
| | Расположение: **limb/macro/src/tags/core/**
[include](./include_tag.md) | Вставляет содержимое другого шаблона или иного файла в текущий шаблон.
[slot](./slot_tag.md) | Представляет из себя место в шаблоне куда может быть вставлен другой шаблон или часть другого шаблона.
[wrap](./wrap_tag.md) | Вставляет часть шаблона в определенное место другого шаблона. Делает из этих двух шаблонов один — единый шаблон. В случае использования совместо с тегами [{{into}}](./into_tag.md) просто указывает на имя шаблона, куда будет вставляться содержимое тегов [{{into}}](./into_tag.md).
[{{template}}](./template_tag.md) | Определяет часть шаблона, которая может быть многократно использована при помощи тега [{{apply}}](./apply_tag.md). Комбинация [{{template}}](./template_tag.md) и [{{apply}}](./apply_tag.md) позволяет делать аналог [{{include}}](./include_tag.md), но только в рамках одного шаблона. Тег [{{template}}](./template_tag.md) без применения тега [{{apply}}](./apply_tag.md) не генерирует код.

## Прочие
Тег | Описание
----|---------
| | Расположение: **limb/macro/src/tags/core/**
[{{apply}}](./apply_tag.md) | Вставляет часть шаблона, указанное в рамках какого-то тега [{{template}}](./template_tag.md). По сути это тот же [{{include}}](./include_tag.md), только включаемый шаблон находится в текущем шаблоне.
[{{into}}](./into_tag.md)	| Вставляет часть шаблона в определенное место другого шаблона, отмеченное тегом [slot](./slot_tag.md). Используется внутри тега [wrap](./wrap_tag.md).
[copy](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroCopyAndCutTagsTest.class.php#L12) | Копирует содержимое в переменную-буффер
[cut](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroCopyAndCutTagsTest.class.php#L21)  | Перемещает содержимое в переменную-буффер
[nospace, trim, space, tab](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroNospaceTagTest.class.php#L11)  | Позволяют вырезать пробельные символы сохраняя наглядность разметки
[repeat](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroRepeatTagTest.class.php#L1) | Повторяет свое содержимое, указанное количество раз
[assign](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroAssignTagTest.class.php#L1) | Копирует содержимое одной переменной в другую
[Тэги if, elseif, else](https://github.com/limb-php-framework/limb/blob/Limb2010.1/macro/tests/cases/tags/core/lmbMacroIfAndElseIfAndElseTagsTest.class.php#L1)	| Ветвление тэгами
