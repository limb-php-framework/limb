<?php
lmb_package_require('dbal');
lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');
lmb_require('limb/core/src/lmbSerializable.class.php');

class lmbCacheDbConnection extends lmbCacheAbstractConnection
{
  /**
   * @var lmbUri
   */
  protected $dsn;
  /**
   * @var lmbTableGateway
   */
  protected $db_table;

  function __construct(lmbUri $dsn)
  {
    parent::__construct($dsn);

    $this->dsn = $dsn;

    if(!$db_dsn_name = $dsn->getHost())
      throw new lmbNoSuchPropertyException('DB DSN name not found in cache DSN', array('cache dsn'=>$dsn));

    if(!$db_table_name = $dsn->getQueryItem('table'))
      throw new lmbNoSuchPropertyException('DB table name not found in cache DSN', array('cache dsn'=>$dsn));

    $db_connection = lmbToolkit::instance()->getDbConnectionByName($db_dsn_name);

    $this->db_table = new lmbTableGateway($db_table_name, $db_connection);
  }

  function getType()
  {
    return 'Db';
  }

  function lock($key)
  {
    $resolved_key = $this->_resolveKey($key);

    $lock = $this->db_table->selectFirstRecord('`key` = \''.$resolved_key.'\'');

    if($lock['is_locked'])
      return false;

    try {
      $this->db_table->insertOnDuplicateUpdate(array(
        'key' => $resolved_key,
        'is_locked' => true,
      ));
      return true;
    }
    catch (Exception $e)
    {
      return false;
    }
  }

  function unlock($key)
  {
    $resolved_key = $this->_resolveKey($key);

    $this->db_table->update(array('is_locked' => false), '`key` = \''.$resolved_key.'\'');
  }

  function add ($key, $value, $ttl = false)
  {
    $resolved_key = $this->_resolveKey($key);

    if($ttl)
      $ttl += time();

    try {
      $this->db_table->insert(array(
        'key' => $resolved_key,
        'value' => lmbSerializable::serialize($value),
        'ttl' => $ttl
      ));
      return true;
    }
    catch (Exception $e)
    {
      return false;
    }
  }

  function set($key, $value, $ttl = false)
  {
    $resolved_key = $this->_resolveKey($key);

    if($ttl)
      $ttl += time();

    try {
      $this->db_table->insertOnDuplicateUpdate(array(
        'key' => $resolved_key,
        'value' => lmbSerializable::serialize($value),
        'ttl' => $ttl
      ));
      return true;
    }
    catch (Exception $e)
    {
      return false;
    }
  }

  function _getSingleKeyValue ($key)
  {
    $resolved_key = $this->_resolveKey($key);

    if(!$record = $this->db_table->selectFirstRecord('`key` = \''.$resolved_key.'\''))
      return NULL;

    $ttl = (int) $record['ttl'];

    if($ttl && $ttl < time())
      return NULL;

    return lmbSerializable::unserialize($record['value']);
  }

  function delete($key)
  {
    $resolved_key = $this->_resolveKey($key);
    $this->db_table->delete('`key` = \''.$resolved_key.'\'');
  }

  function flush()
  {
    $this->db_table->delete();
  }
}
