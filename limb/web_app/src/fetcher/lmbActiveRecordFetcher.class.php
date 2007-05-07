<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordFetcher.class.php 5826 2007-05-07 14:07:42Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/core/src/lmbClassPath.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

class lmbActiveRecordFetcher extends lmbFetcher
{
  protected $class_path;
  protected $record_id;
  protected $record_ids;
  protected $find;
  protected $find_params = array();

  function setClassPath($value)
  {
    $this->class_path = $value;
  }

  function setRecordId($value)
  {
    if(!$value)
      $value = '';
    $this->record_id = $value;
  }

  function setFind($find)
  {
    $this->find = $find;
  }

  function addFindParam($value)
  {
    $this->find_params[] = $value;
  }

  function setFindParams($find_params)
  {
    $this->find_params = $find_params;
  }

  function setRecordIds($value)
  {
    if(!is_array($value))
      $this->record_ids = array();
    else
      $this->record_ids = $value;
  }

  function _createDataSet()
  {
    if(!$this->class_path)
      throw new lmbException('Class path is not defined!');

    $class_path = new lmbClassPath($this->class_path);
    $class_path->import();
    $class_name = $class_path->getClassName();

    if(is_null($this->record_id) && is_null($this->record_ids))
    {
      if(!$this->find)
      {
        return lmbActiveRecord :: find($class_name);
      }
      else
      {
        $method = 'find' . lmb_camel_case($this->find);
        $callback = array($class_name, $method);
        if(!is_callable($callback))
         throw new lmbException('Active record of class "'. $class_name . '" does not support method "'. $method . '"');
        return call_user_func_array($callback, $this->find_params);
      }
    }

    if($this->record_id)
    {
      try
      {
        if($this->find)
        {
          $method = 'find' . lmb_camel_case($this->find);
          $callback = array($class_name, $method);
          if(!is_callable($callback))
            throw new lmbException('Active record of class "'. $class_name . '" does not support method "'. $method . '"');
          $record = call_user_func_array($callback, array($this->record_id));
        }
        else
          $record = lmbActiveRecord :: findById($class_name, $this->record_id);
      }
      catch(lmbARNotFoundException $e)
      {
        $record = array();
      }

      return $this->_singleItemCollection($record);
    }
    elseif($this->record_ids)
    {
      return lmbActiveRecord :: findByIds($class_name, $this->record_ids);
    }

    return new lmbCollection();
  }

  protected function _singleItemCollection($ar)
  {
    return new lmbCollection(array($ar));
  }
}

?>
