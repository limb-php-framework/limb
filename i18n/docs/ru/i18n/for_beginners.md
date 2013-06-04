## Добавление локализации в свой проект
1) В проекте создаем директорию i18n/translations

2) Копируем в нее файлы *.ts из пакетов, в которые предполагается вносить изменения, касающиеся локализации (validation.en_US.ts, validation.ru_RU.ts, cms.en_US.ts, cms.ru_RU.ts)

3) Создаем файлы (main.ru_RU.ts, main.en_US.ts) со следующим содержанием:

    <?xml version=«1.0» encoding=«utf-8»?> <TS> <context> </context> </TS>

4) Выполняем команду (обновление словарей):

    limb i18n ./path/to/file.ts ./template

5) Очищаем кэш, обновляем страницу

## Локализация

1) В PHP коде        
    
    lmb_i18n(«Hello {arg}», array('arg' ⇒ 'Bob'), «domain»)
    lmb_i18n(«Hello {arg}», array('arg' ⇒ 'Bob'), «domain»)

2) В шаблоне

Фильтр:

    {$query|i18n:"project_name"}
    {$query|i18n:"project_name", "some_domain"}
    {$query|i18n:"project_name", "arg", "Bob"}
    
Тэг:

    {{i18n text="Search query"}} //будет использоваться домен main
    {{i18n text="Search query" domain="some_domain"}}
