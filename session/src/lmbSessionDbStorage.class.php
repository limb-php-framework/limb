<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/session/src/lmbSessionStorage.interface.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

/**
 * lmbSessionDbStorage store session data in database.
 * lmb_session db table used to store session data.
 * The structure of lmb_session db table can be found in limb/session/init/ folder.
 * @todo Check client ip while reading session.
 * @todo Allow to set any db table name to store session data in.
 * @see lmbSessionStartupFilter
 * @version $Id: lmbSessionDbStorage.class.php 7486 2009-01-26 19:13:20Z pachanga $
 * @package session
 */
class lmbSessionDbStorage implements lmbSessionStorage
{
  /**
   * @var lmbSimpleDb facade to work with database
   */
  protected $db;
  /**
   * @var integer maximum session life time
   */
  protected $max_life_time = null;

  /**
   *  Constructor.
   *  @param lmbDbConnection database connection object
   */
  function __construct($db_connection)
  {
    $max_life_time = lmb_env_get('LIMB_SESSION_DB_MAX_LIFE_TIME');

    $this->max_life_time = $max_life_time;

    $this->db = new lmbSimpleDb($db_connection);
  }

  /**
   * @see lmbSessionStorage :: install()
   * @return void
   */
  function install()
  {
    session_set_save_handler(
     array($this, 'storageOpen'),
     array($this, 'storageClose'),
     array($this, 'storageRead'),
     array($this, 'storageWrite'),
     array($this, 'storageDestroy'),
     array($this, 'storageGc')
    );
  }

  /**
   * Opens session storage
   * Does nothing and returns true
   * @return boolean
   */
  function storageOpen()
  {
    return true;
  }

  /**
   * Closes session storage
   * Does nothing and returns true
   * @return boolean
   */
  function storageClose()
  {
    return true;
  }

  /**
   * Read a single row from <b>lmb_session</b> db table and returns <b>session_data</b> column
   * @param string session ID
   * @return mixed
   */
  function storageRead($session_id)
  {
    $rs = $this->db->select('lmb_session', new lmbSQLFieldCriteria('session_id', $session_id));
    $rs->rewind();
    if($rs->valid())
      return $rs->current()->get('session_data');
    else
      return false;
  }

  /**
   * Creates new or updates existing row in <b>lmb_session</b> db table
   * @param string session ID
   * @param mixed session data
   * @return void
   */
  function storageWrite($session_id, $value)
  {
    $crit = new lmbSQLFieldCriteria('session_id', $session_id);
    $rs = $this->db->select('lmb_session', $crit);

    $data = array('last_activity_time' => time(),
                  'session_data' => $value);

    $this->db->begin();
    if($rs->count() > 0)
      $this->db->update('lmb_session', $data, $crit);
    else
    {
      $data['session_id'] = "{$session_id}";
      $this->db->insert('lmb_session', $data, null);
    }
    $this->db->commit();
  }

  /**
   * Removed a row from <b>lmb_session</b> db table
   * @param string session ID
   * @return void
   */
  function storageDestroy($session_id)
  {
    $this->db->begin();
    $this->db->delete('lmb_session',
                      new lmbSQLFieldCriteria('session_id', $session_id));
    $this->db->commit();
  }

  /**
   * Checks if storage is still valid. If session if not valid - removes it's row from <b>lmb_session</b> db table
   * Prefers class attribute {@link $max_life_time} if it's not NULL.
   * @param integer system session max life time
   * @return void
   */
  function storageGc($max_life_time)
  {
    if($this->max_life_time)
      $max_life_time = $this->max_life_time;

    $this->db->begin();
    $this->db->delete('lmb_session',
                      new lmbSQLFieldCriteria('last_activity_time', time() - $max_life_time, lmbSQLFieldCriteria::LESS));
    $this->db->commit();
  }
}

