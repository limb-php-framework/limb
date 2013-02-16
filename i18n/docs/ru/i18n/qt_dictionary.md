# Работа с QT словарями
Qt словари хранятся в виде XML файлов по следующему пути *i18n/translations/domain.locale.ts*. Например, *i18n/translations/default.ru_RU.ts*. У пакетов имеются свои словари, которые хранятся в соответствующих файлах *package/i18n/translations/package.locale.ts*

Пример содержания словаря default.en_US.ts:

    <?xml version="1.0" encoding="utf-8"?>
    <!DOCTYPE TS>
    <TS version="2.0" language="en_US" sourcelanguage="ru_RU">
    <context>
        <name></name>
        <message>
            <source>Галлерея</source>
            <translation>Gallery</translation>
        </message>
        <message>
            <source>Пол</source>
            <translation>Gender</translation>
        </message>
        <message>
            <source>Главная</source>
            <translation>Home</translation>
        </message>
    </context>
    </TS>

Обратите внимание, что в аттрибуте language указывается язык перевода, а в *sourcelanguage* язык исходных фраз.

В ситуации, когда нет необходимости переводить или уточнять исходные фразы на том же языке, можно создать словарь без переводов *default.ru_RU.ts*:

    <?xml version="1.0" encoding="utf-8"?>
    <!DOCTYPE TS>
    <TS version="2.0" language="ru_RU" sourcelanguage="ru_RU">
    <context>
        <name></name>
        <message>
            <source>Галлерея</source>
        </message>
        <message>
            <source>Пол</source>
        </message>
        <message>
            <source>Главная</source>
        </message>
    </context>
    </TS>

QT Файлы хранятся в UTF-8 кодировке.

## Перевод фраз
Перевод таких файлов осуществляется при помощи достаточно удобной десктопной программы Qt Linguist от фирмы [Trolltech](http://www.trolltech.com/). [Скачать Qt Linguist](http://qt-apps.org/content/show.php/Qt+Linguist+Download?content=89360).
