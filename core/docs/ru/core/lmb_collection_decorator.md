# lmbCollectionDecorator
Класс lmbCollectionDecorator — базовый класс, реализующий [lmb_collection_interface](./lmb_collection_interface.md), используется в качестве базового при реализации декораторов на коллекции.

    class SomeIteratorDecorator extends lmbCollectionDecorator 
    {
      function rewind()
      {
        parent :: rewind();
 
        $this->_initImportantData();
      }
 
      function current()
      {
        $record = parent :: current();
 
        $this->_processRecord($record);
 
        return $record;
      }
 
      protected function _processRecord($record)
      {
        [...]
      }
 
      protected function _initImportantData()
      {
        [...]
      }
    }

Часто на практике **появляется необходимость полностью заменить декорируемый итератор**. Например, нам нужно сделать какую-либо выборку из базы данных, но для этого нам нужно знать содержимое всех элементов декорируемого итератора. Обычно это делается в методе rewind(). Не забудьте вызвать метод rewind() у нового декорируемого итератора, а также использовать какой-либо флаг для того чтобы знать, была ли проведена обработка или нет.

    class SomeIteratorDecorator extends lmbCollectionDecorator
    {
      protected $processed = false;
 
      function rewind()
      {
        parent :: rewind();
 
        if(!$this->processed && $this->iterator->valid())
        {
          [...do some processing...]
          $iterator = [...do some processing...]
          $this->processed = true;
        }
        else
          $iterator = new lmbCollection();
 
        $this->iterator = $iterator;
 
        return $this->iterator->rewind();
      }
