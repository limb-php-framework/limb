# Шаг 4. Создание и отображение списка товаров для администраторов
## Часто используемые шаблоны
Так как мы уже умеем создавать модели, контроллеры и шаблоны, то позволим себе немного расслабится, и воспользоваться возможностью [генерации сущностей](../../../../constructor/docs/ru/constructor.md):

    $ php ./lib/limb/limb.php entity_create
    Project directory [/www/limb-example-shop]: 
    Limb directory [/www/limb-example-shop/lib/]: 
    Table name (could be 'all'): product

После выполнения этих действий у нас должны были появится модель, контроллер и шаблоны, построенные по таблице **product**.

## Класс Product
Немного доработаем сгенерированную модель.

Файл shop/src/model/Product.class.php:

    <?php
    class Product extends lmbActiveRecord
    {
      protected $_default_sort_params = array('title' => 'ASC');
 
      protected function _createValidator()
      {
        $validator = new lmbValidator();
        $validator->addRequiredRule('title');
        $validator->addRequiredRule('description');
        $validator->addRequiredRule('price');
        return $validator;
      }
 
      function getImagePath()
      {
        if(($image_name = $this->getImageName()) && file_exists(lmb_env_get('PRODUCT_IMAGES_DIR') . $image_name))
          return lmb_env_get('LIMB_HTTP_BASE_PATH') . 'product_images/' . $image_name;
        else
          return lmb_env_get('LIMB_HTTP_BASE_PATH') . '/shared/cms/images/icons/cancel.png';
      }
    }

Мы установили сортировку продуктов по-умолчанию при помощи атрибута **$_default_sort_params**. Теперь при выборках продукты будут сортироваться по заголовку в алфавитном порядке. Подробнее о сортировке по-умолчанию можно узнать в разделе [«Поиск и сортировка объектов»](../../../../active_record/docs/ru/active_record/find.md).

Мы также создали метод **getImagePath()**. Этот метод по сути вводит новое **виртуальное поле $image_path**, которое можно использовать в шаблонах (пример будет показан ниже). Поле $image_path мы ввели для того, чтобы не выносить знания о папке product_images/ в шаблоны, и при желании эту папку можно будет легко сменить. Если товар не имеет изображения, тогда будет выводиться иконка cancel.png из набора, входящего в состав пакета CMS.

## Создание, редактирование и удаление товаров из панели управления
Благодаря генерации у нас уже есть контроллер AdminProductController и заготовки шаблонов для панели управления. Мы не будем подробно описывать наши действия, надеясь на опыт создания подобного функционала для сущности User.

### Доработка контроллера AdminProductController
Уже сейчас мы можем создавать и редактировать товары, благодаря использованию lmbAdminObjectController, в качестве базового. Но, к сожалению, он не знает как мы храним изображения. И именно этому его придется научить.

Файл shop/src/controller/AdminProductController.class.php:

    <?php
    lmb_require('limb/cms/src/controller/lmbAdminObjectController.class.php');
    lmb_require('limb/util/system/lmbFs.class.php');
 
    class AdminProductController extends lmbAdminObjectController
    {
      protected $_object_class_name = 'Product';
 
      protected function _onBeforeSave()
      {
        return $this->_uploadImage($this->item, $this->request->get('image'));
      }
 
      function _uploadImage($item, $uploaded_image)
      {
        if(!$uploaded_image)
          return;
 
        if(!$uploaded_image['name'] || !$uploaded_image['tmp_name'])
          return;
 
        $file_name = $uploaded_image['name'];
        $file_path = $uploaded_image['tmp_name'];
 
        $dest_path = lmb_env_get('PRODUCT_IMAGES_DIR') . $file_name;
        lmbFs :: cp($file_path, $dest_path);
 
        unlink($file_path);
 
        $item->setImageName($file_name);
      }
    }

Метод _onAfterImport() является специальным методом (без реализации) в lmbAdminObjectController и вызывается непосредственно после импортирования свойств в сохраняемый объект (см. lmbAdminObjectController :: _import()). Непосредственно обработку изображения мы выделили в отдельный метод _uploadImage(). Переменная $_FILES обрабатывается в классе lmbHttpRequest, поэтому мы можем получить данные по изображению через объект $request. Загруженное изображение сохраняется в папке, определенной переменной окружения PRODUCT_IMAGES_DIR, о которой мы говорили в [шаге 2](./step2.md).

### Первый результат
Попробуйте теперь зайти на страницу /admin_product и «поиграть» с ней, например, создать с десяток товаров, отредактировать и удалить некоторых из них.

Если у вас нет желания добавлять товары самостоятельно, то можете скопировать содержимое shop_example_dir/www/product_images к себе в проект и залить db.mysql из shop_example_dir/init/ к себе в базу данных проекта. Мы позволили себе немного позаимствовать описания книг с Amazon.com

## Смена статуса доступности товара
Иногда владельцу магазина необходимо временно снять с продажи сразу несколько товаров. Снимать галочки в форме редактирования для каждого товара - занятие довольно утомительное, поэтому мы реализуем возможность смены статуса доступности товаров сразу для нескольких товаров прямо из списка товаров.

Для этого нам необходимо будет немного модифицировать шаблон shop/template/admin_product/display.html:

    [...]
          <div class="list">
            <div class='list_actions'>
              {{apply template="selectors_button" action="set_available" title="Set available" /}}
              {{apply template="selectors_button" action="set_unavailable" title="Set unavailable" /}}
            </div>
            <table>
              <tr>
                <th>{{apply template="selectors_toggler"/}}</th>
                <th>#ID</th>
                <th width='20%'>Title</th>
                <th width='10%'>Price</th>
                <th width='10%'>Image</th>
                <th width="10%">Availability</th>
                <th width='40%'>Decsription</th>
                <th>Actions</th>
              </tr>
              {{list:item}}
                <tr class='{$parity}'>
                  <td>{{apply template="selector" value="{$item.id}"/}}</td>
                  <td>#{$item.id}</td>
                  <td>{$item.title}</td>
                  <td>{$item.price}</td>
                  <td><img src="{$item.image_path}"/></td>
                  <td><img src="/shared/cms/images/icons/<?= ($item->getIsAvailable()) ? 'lightbulb.png' : 'lightbulb_off.png'; ?>"/></td>
                  <td>{$item.description|raw|nl2br}</td>
                  <td class='actions'>
                    {{apply template="object_action_edit"  item="{$item}" icon="page_white_edit" /}}
                    {{apply template="object_action_delete" item="{$item}" icon="page_white_delete" /}}
                  </td>
                </tr>
              {{/list:item}}
              {{list:empty}}
                <div class="empty_list">Empty</div>
              {{/list:empty}}
            </table>
            <div class='list_actions'>
              {{apply template="selectors_button" action="set_available" title="Set available" /}}
              {{apply template="selectors_button" action="set_unavailable" title="Set unavailable" /}}
            </div>
          </div>
    [...]

Мы добавили div'ы с кнопками сверху и снизу списка, а так же checkbox-ы для каждой строки.

Примененный нами (через тег apply) шаблон **selectors_button** предназначен как раз для таких групповых действий. Он рисует кнопку выводящую popup-окно для указанного действия (set_available и set_unavailable в нашем случае)

Итак, теперь нужно добавить два новых действия в контроллер AdminProductController:

    class AdminProductController extends lmbController
    {
      [...]
      function doSetAvailable()
      {
        return $this->_changeAvailability(true);
      }
 
      function doSetUnavailable()
      {
      	return $this->_changeAvailability(false);
      }
 
      protected function _changeAvailability($is_available)
      {
        if(!$ids = $this->request->getArray('ids'))
        {
          $this->_endDialog();
          return;
        }
 
        $products = lmbActiveRecord :: findByIds('Product', $ids);
        foreach($products as $product)
        {
          $product->setIsAvailable((int) $is_available);
          $product->save();
        }
 
        $this->_endDialog();
      }
    }

Здесь мы воспользовались find()-методом класса lmbActiveRecord :: **findByIds($class_name, $ids, $params = array())**, который позволяет загрузить сразу несколько объектов по их идентификаторам.

Справедливости ради отметим, что в данном случае, возможно, можно было обойтись простым UPDATE запросом к базе данных, чем сначала загружать объекты в память и изменять их по одному. Да, в простых случаях так действовать можно для оптимизации быстродействия, однако если с изменением статусов связаны какие-либо бизнес-правила, мы рекомендуем все же использовать API класса lmbActiveRecord.

Покажем как бы выглядел код данного метода, если бы мы гнались за скоростью выполнения:

    [...]
 
      protected function _changeAvailability($is_available)
      {
        if(!$ids = $this->request->getArray('ids'))
        {
          $this->_endDialog();
          return;
        }
 
        $is_available = (int) $is_available;
 
        $sql = 'UPDATE product SET is_available = ' . $is_available. 
               ' WHERE id IN (' . implode(',', array_map('intval', $ids)) . ')';
        $this->toolkit->getDefaultDbConnection()->execute($sql);
     
        $this->_endDialog();
      }
    [...]

Для выполнения запроса к базе данных напрямую нам потребовался явный доступ к объекту $connection, который можно получить из toolkit-а при помощи метода **getDefaultDbConnection()**.

Для безопасности мы также пропустили каждый элемент массива $ids через метод intval.

## Далее
Итак, следующий шаг: [Шаг 5. Отображение списка товаров для покупателей. Поиск товаров](./step5.md)
