# lmbStaticTools
**lmbStaticTools** — набор инструментов, который всегда возвращает предопределенный результат. Часто используется в тестах для изменения поведения других инструментов [инструментария (toolkit)](../toolkit.md), например:

    class UOWFilterTest extends lmbTestCase
    {
      function setUp()
      {
        $this->uow = new MockUnitOfWork();
 
        lmbToolkit :: save();
        lmbToolkit :: merge(new lmbStaticTools(array('getUOW' => $this->uow)));
 
      }
 
      function tearDown()
      {
        lmbToolkit :: restore();
      }
      [...]
    }
