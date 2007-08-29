<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbSerializable.class.php');


/**
 * Wrapper class for global $_SESSION variable
 *
 * @see lmbWebAppTools :: getSession()
 * @version $Id: lmbSession.class.php 6243 2007-08-29 11:53:10Z pachanga $
 * @package session
 */
class lmbSession
{
  /**
   * @var array variables names that were changed. Used for testing purposes mostly.
   */
  protected $touched_names = array();

  /**
   * Starts session and installs driver
   * @param object Concrete session driver
   */
  function start($storage = null)
  {
    if($storage)
      $storage->install();
    session_start();
  }

  /**
   * Register a variable in session and returns a reference to it.
   * @param string variable name
   */
  function & registerReference($name)
  {
    if(!isset($_SESSION[$name]))
      $_SESSION[$name] = '';

    $this->touched_names[$name] = true;
    return $_SESSION[$name];
  }

  /**
   * Returns variable value from session
   * @param string variable name
   * @param mixed value that should be returned if variable is not registered in session yet
   * @return mixed
   */
  function get($name, $empty_value = null)
  {
    if(!isset($_SESSION[$name]))
      return $empty_value;

    if(is_object($_SESSION[$name]) && $_SESSION[$name] instanceof lmbSerializable)
      return $_SESSION[$name]->getSubject();
    else
      return $_SESSION[$name];
  }

  /**
   * Sets variable into session
   * Automatically wraps objects with {@link lmbSerializable}
   * that helps to prevent <b>"class is not defined"</b> error while restoring session
   * @param string variable name
   * @param mixed value
   * @return void
   */
  function set($name, $value)
  {
    if(is_object($value))
      $_SESSION[$name] = new lmbSerializable($value);
    else
      $_SESSION[$name] = $value;

    $this->touched_names[$name] = true;
  }

  /**
   * Clears session
   * @return void
   */
  function reset()
  {
    $_SESSION = array();
  }

  /**
   * Returns TRUE if session has such variable
   * @param string variable name
   * @return boolean
   */
  function exists($name)
  {
    return isset($_SESSION[$name]);
  }

  /**
   * Removes variable from session
   * @param string variable name
   * @return void
   */
  function destroy($name)
  {
    if(isset($_SESSION[$name]))
      unset($_SESSION[$name]);
  }

  /**
   * Alias for destroy() method
   * @see destroy()
   * @return void
   */
  function remove($name)
  {
    $this->destroy($name);
  }

  /**
   * Returns session data
   * @return array
   */
  function export()
  {
    return $_SESSION;
  }

  /**
   * Removed all variables that were changed
   * @see $touched_names
   * @return void
   */
  function destroyTouched()
  {
    foreach(array_keys($this->touched_names) as $name)
      $this->destroy($name);
  }

  /**
   * Dumps session data (for debugging purposes mostly)
   * @return mixed
   */
  function dump()
  {
    $str = "";
    if(!isset($_SESSION) || !is_array($_SESSION))
      return $str;

    foreach($_SESSION as $key => $value)
    {
      $str .= "$key : ";
      if(is_array($value))
        $str .= 'ARRAY[' . sizeof($value) . ']';
      elseif(is_object($value))
        $str .= 'OBJECT(' . get_class($value) . ')';
      else
        $str .= strtoupper(gettype($value));
      $str .= "\n";
    }
    return $str;
  }
}

