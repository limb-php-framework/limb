<?php

lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

class lmbCronJobLogger
{
  const STATUS_START = 'START';
  const STATUS_SUCCESS = 'SUCCESS';
  const STATUS_ERROR = 'ERROR';
  const STATUS_EXCEPTION = 'EXCEPTION';
  const STATUS_CONFLICT = 'CONFLICT';

  protected $cron_job_name = 'unknown';
  public $table_name = 'lmb_cron_log';

  /**
   * @var lmbMysqliConnection
   */
  protected $conn;

  function __construct()
  {
    $this->conn = lmbToolkit::instance()->getDefaultDbConnection();
  }

  /**
   * @return lmbTableGateway
   */
  protected function _getTable()
  {
    return new lmbTableGateway($this->table_name, $this->conn);
  }

  protected function _makeRecord($status, $info = '')
  {
    $record  = array(
      'name'    => $this->cron_job_name,
      'time'    => time(),
      'status'  => $status,
      'info'    => $info
    );
    $table  = $this->_getTable();
    $table->insert($record);
  }

  function setJobName($job_name)
  {
  	$this->cron_job_name = $job_name;
  }

  function getRecords($count = false)
  {
    $table = $this->_getTable();
    $rs = $table->select(new lmbSQLCriteria('name = ?', array($this->cron_job_name)), array('id' => 'DESC'));
    if($count)
      $rs->paginate(0, $count);
    return $rs;
  }

  function makeStartRecord($output = '')
  {
    $this->_makeRecord(self::STATUS_START, $output);
  }

  function makeConflictRecord($output = '')
  {
    $this->_makeRecord(self :: STATUS_CONFLICT, $output);
  }

  function makeEndRecord($error, $output = '')
  {

    if(null === $error)
      $this->_makeRecord(self::STATUS_SUCCESS);
    else
    {
      if(!is_string($error))
        $error = var_export($error, true);

      if($output)
        $error .= PHP_EOL . $output;

      $this->_makeRecord(self::STATUS_ERROR, $error);
    }

    $this->is_ended = true;
  }

  function makeExceptionRecord($info)
  {
    $this->_makeRecord(self::STATUS_EXCEPTION, $info);
  }

}
