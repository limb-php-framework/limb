# lmbTableGateway
**lmbTableGateway** — класс, инкапсулирующий работу с одной таблицей базы данных.

lmbTableGateway содержит 3защищенных метода, которые можно перекрывать в дочерних классах для определения деталей таблицы:

* **_defineDbTableName()** — позволяет указать название таблицы. Если этот метод не перекрыт, то lmbTableGateway отрезает часть DbTable от названия класса (например, CatalogDbTable) и использует первую часть в качестве названия таблицы.
* **_definePrimaryKeyName()** — позволяет указать имя поля с первичным ключом. По-умолчанию равен 'id'.
* **_defineConstraints()** — позволяет описать связи данной таблицы с другими таблицами. Используется при каскадном удалении. Полезно использовать с ранними версиями MySQL, где не поддерживается каскадное удаление.

Список полей таблицы lmbTableGateway получает автоматически.

## Определение связанных таблиц (Constraints)
Метод **_defineConstraints()** возвращает массив вида «имя поля» ⇒ «массив связей». «массив связей» имеет такую структуру array(«table_name» ⇒ «название таблицы», «field» ⇒ «название поля»).

Пример ниже.

## Пример lmbTableGateway

require_once(LPKG_CORE_DIR . '/core/db/lmbTableGateway.class.php');
 
    class PlayerDbTable extends lmbTableGateway
    {
      function _defineDbTableName()
      {
        return 'player';
      }
 
      function _defineConstraints()
      {
        return array(
          'id' =>  array(
          0 => array(
                'table_name' => 'contract',
                'field' => 'player_id',
              ),
          ),
        );
      }
    }

## Использование lmbTableGateway
Список часто используемых методов:

* deleteById($id) — удаляет запись из таблицы с указанным идентификатором.
* delete($criteria) — удаляет записи из таблицы по условию. Условие здесь — это объект из подсистемы [Criteria](./criteria.md).
* updateById($id, $data) — обновляет запись в таблице с указанным идентификатором.
* update($row, $сriteria) — обновляет записи в таблице, удовлетворяющие услови. Условия определяются так же, как и для удаления.
* selectRecordById($id) — возвращает запись из таблицы с указанным идентификатором. Запись — это объект, поддерживающий интерфейс DataSource.
* select($criteria, $sort_params, $fields) — вовзращает записи из таблицы, удовлетворяющие условию. Условие здесь — это объект из подсистемы [Criteria](./criteria.md).
