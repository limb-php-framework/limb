# lmbMockToolsWrapper
**lmbMockToolsWrapper** — Враппер для внедрения моков в [lmb_toolkit](./lmb_toolkit.md). Позволяет создавать моки на другие набор инструментов, но также указывать, какие методы делегировать моку.

Пример использования:

    class SomeDAOClientTest extends UnitTestCase
    {
      protected $dao;
 
      function setUp()
      {
       $this->dao = new MockSpecialTestingDAO();
 
       $this->tools = new MockLimbBaseTools();
 
       lmbToolkit :: save();
       lmbToolkit :: merge(new lmbMockToolsWrapper($this->tools, array('createDAO')));
      }
 
      function tearDown()
      {
        lmbToolkit :: restore();
      }
 
      function testSome()
      {
        [...]
        $this->dao->expectOnce('createDAO', $params);
      }
    }

Задача lmbMockToolsWrapper — реализация метода getToolsSignatures(). Обратите внимание, что lmbToolkit :: merge() не создает новую копию инструментария, не запонимает старую, а лишь заменяет текущую копию. Поэтому в тестах перед merge() необходимо вызывать save().
