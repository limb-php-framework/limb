# Работа с формами. Валидация данных, пришедших с форм
## Типичная схема работы с формой

* При отображении страницы в формной в первый раз необходимо заполнить поля формы первоначальными данными
* При отправке формы необходимо проверить данные
* Если данные введены неверно — необходимо прервать дальнейшую обработку в контроллере и отобразить на той же самой страницы список ошибок валидации. При этом данные в полях формы необходимо сохранить.
* Если все данные введены верно — завершить обработку данных в контроллере.

Рассмотрим пример действия контроллера, который мы взяли из [CRUD примера](../../../../docs/ru/tutorials/basic.md):

    <?php
    class NewsController extends lmbController
    {
      [...]
      function doEdit()
      {
        $news = new News((int)$this->request->get('id'));
 
        $this->useForm('news_form');
        $this->setFormDatasource($news);
 
        if(!$this->request->hasPost())
          return;
 
        $news->import($this->request);
 
        if($news->trySave($this->error_list))
          $this->redirect();
      } 
    ?>

Разберем наиболее важные моменты:

* При помощи $this→useForm('news_form') мы указали, что контроллер в данном случае будет работать с активным компонентом

WACT-шаблона «news_form».

* $this→setFormDatasource($news) — передали $news в качестве источника первоначальных данных для формы. Так как ниже идет $news→import($this→request), то есть в новость добавляются все данных из $request-а, мы никогда не потеряем данных в полях формы в случае, если часть из них была введена неверно.
* $news→trySave($this→error_list) — здесь мы фактически произвели валидацию данных. Если trySave() возвращает false это будет значить, что news содержит неверные данные. Ошибки валидации будут содержаться в $this→error_list, который уже был передан по $view в качестве контейнера ошибок для формы «news_form» в WACT-шаблоне.

Шаблоны форм

При использовании WACT-шаблонизатора формы описываются при помощи обычных тегов <form> с указанием атрибута *runat='server'*, например:

    <form id='news_form' name='news_form' method='post' runat='server'>
 
      <core:INCLUDE file='form_errors.html'/>
 
      <label for="title">Заголовок</label>
      <input type="text" name="title" size="40" title='Заголовок'>
 
      <br/>
      <input id='cancel' type='button' value="Отменить" onclick='window.close();return false;'>
      <input id='save' type='button' value="Создать/Изменить">
    /form>
    
Обратите внимание на атрибут **title** <input> тега. Значение этого атрибута используется при отображении ошибок валидации. Например, вместо «title обязателty к заполнению», выведентся «Заголовок обязателен к заполнению».

Вывод ошибок валидации

Для вывода ошибок использутеся тег [<form:errors>](../../../../macro/docs/ru/macro/tags/form_tags/form_errors_tag.md) (в некоторых случаях <form:field_errors>), который передает список ошибок валидации из формы, внутри которой находится этот тег, в компонент, указанный атрибутом target, например:

    <form id='news_form' name='news_form' method='post' runat='server'>
    [...]
    <form:errors target='errors'/>
 
    <list:list id='errors'>
    Ошибки валидации:
    <ul>
    <list:ITEM>
      <li>{$message}</li>
    </list:ITEM>
    </ul>
    </list:list>
 
    [...]
    </form>

## Валидация
Подробнее про валидацию данных, пришедних с формы, лучше почитать на странице [«Валидация данных»](./validation.md).

## Что еще почитать?
* [Теги форм с активными компонентами или без](../../../../macro/docs/ru/macro/form_tags.md)
* [MACRO тег <form>](../../../../macro/docs/ru/macro/tags/form_tags/form_tag.md)
