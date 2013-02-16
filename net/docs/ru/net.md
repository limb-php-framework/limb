# Пакет NET
Пакет NET — набор классов в для работы с сетевыми протоколами(в основном с HTTP)

## Классы пакета

Класс | Назначение
------|-----------
[lmbHttpRequest](./net/lmb_http_request.md) | Инкапсулирует HTTP Запрос к приложению.
[lmbUri](./net/lmb_uri.md) | Используются для работы с URL-ами.
[lmbHttpResponse](./net/lmb_http_response.md)	| Инкапсулирует HTTP Ответ от системы.
[lmbHttpCache](./net/lmb_http_cache.md) | Используется для выдачи правильного HTTP Ответа системы пользователю в случае, если можно использовать кеш, хранимый в браузере, например, когда данные не изменялись.
[lmbUploadedFilesParser](./net/lmb_uploaded_files_parser.md) | Утилитарный класс. Используется внутри [lmbHttpRequest](./net/lmb_http_request.md) для обработка переменной $_FILES.
