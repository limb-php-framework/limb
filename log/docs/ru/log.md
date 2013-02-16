# Пакет LOG
Получение объекта для логирования

    $log = lmbToolkit::instance()->getLog();

Запись сообщения в лог

    /**
     * Write message in log
     * 
     * @param string $message log message
     * @param int $level
     * @param array $params log additional params
     * @param lmbBacktrace $backtrace log backtrace, creating new if not assigned 
     * @param string $entry_title log entry title
     *
     * lmbToolkit::instance()->getLog()->log($message, $level = LOG_INFO, $params = array(), $backtrace = null, $entry_title = null)
     *
     */
    $log->log('default', LOG_INFO, array(), null, 'default');

В качестве уровня важности ($level) сообщений используются стандартные константы

* LOG_NOTICE
* LOG_WARNING
* LOG_INFO
* LOG_ERR

Список доступных средств для логирования

Writer | Описание | dsn
-------|----------|----
EchoWriter | Вывод в stdout	| echo:
FileWriter | Запись в файл, по умолчанию это LIMB_VAR_DIR/error.log	| file://path/log/error.log
FirePHPWriter | Вывод в консоль [FirePHP](http://www.firephp.org/) | firePHP://localhost/?check_extension=0
PHPLogWriter | Вывод в PHP лог | PHPLog:
SyslogWriter | Вывод в syslog | syslog:

## FirePHP
Параметр check_extension определяет будет ли проводиться проверка на наличие у клиента дополнения FirePHP в браузере FireFox

## Конфигурирование
**log.conf.php**

    $conf = array(
        'logs' => array(
            'default' => array(
                'firePHP://localhost/?check_extension=1',
                'file://'.lmb_env_get('LIMB_VAR_DIR').'/log/error.log',
            ),
            'debug' => array(
                'firePHP://localhost/?check_extension=0',
            ),
            'test' => array(
                'file://'.lmb_env_get('LIMB_VAR_DIR').'/log/test.log',
            ),
        )
    );

Секция default обязательна и используется методом log() класса lmbLog

Остальные секции могут быть использованы методом log класса lmbLogTools

    /**
     * Write message in log
     * 
     * @param string $message log message
     * @param int $level
     * @param array $params log additional params
     * @param lmbBacktrace $backtrace log backtrace, creating new if not assigned 
     * @param string $log_name log setting section name
     *
     * lmbToolkit::instance()->log($message, $level = LOG_INFO, $params = array(), lmbBacktrace $backtrace = null, $log_name = 'default')
     *
     */
    lmbToolkit::instance()->log('test', LOG_INFO, array(), null, 'test');

Константы

Константа | Значение по умолчанию | Описание
----------|-----------------------|---------
LIMB_LOG_ENABLE | true | Выключение логирования
