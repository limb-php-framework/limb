# lmbSetInterface
**lmbSetInterface** — интерфейс единичных контейнеров данных в Limb3. см. также [Контейнеры данных в Limb3](./data_containers.md).

lmbSetInterface расширяет стандартный SPL интерфейс [ArrayAccess](http://php.net/ArrayAccess):

    interface lmbSetInterface extends ArrayAccess
    {
      function get($name);
      function set($name, $value);
      function remove($name);
      function reset();
      function export();
      function import($values);
      function has($name);
    }

lmbSetInterface используется в [lmbObject](.lmb_object.md), [lmbSet](.lmb_set.md), а также в пакете DBAL (lmbDBRecord)
