# lmbHttpRequest
lmbHttpRequest — инкапсулирует HTTP запрос к системе.

    $request = new lmbHttpRequest();
    $foo = $request->get('foo');
    $image_id = $request->getInteger('image_id');//будет сделано явное преобразование к int типу
    $cookie_value = $request->getCookie('important_cookie');

Если аттрибуты HTTP запроса не были явно переданы в конструкторе, содержит $_GET, $_POST, $_FILES и $_COOKIE данные и URL страницы, с которой был создан запрос к системе, в виде объекта класса [lmbUri](./lmb_uri.md). $_FILES данные приходят особым образом, поэтому они нормализуются при помощи класса [lmbUploadedFilesParser](./lmb_uploaded_files_parser.md).

Во время тестирования lmbHttpRequest удобно вручную иницилизировать непосредственно через конструктор:

    $request = new lmbHttpRequest('http://site.com', $get = array('foo' => 3), $post = array('bar' => 2), $cookies = array('zoo' => 5));
