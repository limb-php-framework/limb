# Поддержка транзакций
## Явный старт и завершение транзакции
Интерфейс lmbDbConnection, который реализуют все классы-подключения к базе данных, содержит методы для управления транзакциями:

* **beginTransaction()** — начинает транзакцию
* **commitTransaction()** — подтверждает и завершает транзакцию
* **rollbackTransaction()** — откатывает назад изменения в рамках текущей транзации

Например:

    lmbToolkit :: instance()->getDefaultDbConnection()->beginTransaction();

## Автоматический старт и завершение транзакции
В пакете DBAL есть специальный класс **lmbAutoTransactionConnection**, который является декоратором на connection и который автоматически стартует транзакцию в случае выполнения любого запроса на изменение базы данных.

Для того, чтобы lmbAutoTransactionConnection работал необходимо встроить специальный фильтр [lmbAutoDbTransactionFilter](./lmb_autodb_transaction_filter.md) в свою цепочку фильтров приложения, например:

    <?php
    lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
    lmb_require('limb/core/src/lmbHandle.class.php');
 
    class MyApplication extends lmbFilterChain
    {
      function __construct()
      {
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbUncaughtExceptionHandlingFilter',
                                            array(dirname(__FILE__) . '/../www/500.html')));
     
        $this->registerFilter(new lmbHandle('limb/dbal/src/filter/lmbAutoDbTransactionFilter'));
 
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));
        $this->registerFilter(new lmbHandle('src/filter/rtRequestDispatchingFilter'));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));
        $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));
      }
    }
    ?>
