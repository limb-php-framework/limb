<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

/**
 * class lmbCmsFileObject.
 *
 * @package cms
 * @version $Id: lmbCmsFileObject.class.php 5998 2007-06-18 12:28:49Z pachanga $
 */
class lmbCmsFileObject extends lmbActiveRecord
{
  protected $_has_one = array('node' => array('field' => 'node_id',
                                              'class' => 'lmbCmsNode',
                                              'can_be_null' => true));

  protected $_db_table_name = 'file_object';

  function _onBeforeSave()
  {
    $this->_ensureUid();

    parent :: _onBeforeSave();
  }

  function loadFile($file_path)
  {
    $this->_ensureUid();

    $this->_store($file_path);
    $this->setSize(filesize($file_path));
  }

  function _ensureUid()
  {
    if(!$this->getUid())
      $this->generateAndSetUid();
  }

  static function findByUid($class_name, $uid, $conn = null)
  {
    return lmbActiveRecord :: findFirst($class_name,
                                        array('criteria' => new lmbSQLFieldCriteria('UID', $uid)),
                                        $conn);
  }

  static function findForParentNode($parent, $conn = null)
  {
    $sql = 'SELECT file_object.* '.
           ' FROM file_object LEFT JOIN node ON node.id = file_object.node_id '.
           ' WHERE node.parent_id = '. $parent->id;

    if(!is_object($conn))
      $conn = lmbActiveRecord :: getDefaultConnection();

    $stmt = $conn->newStatement($sql);
    return lmbActiveRecord :: decorateRecordSet($stmt->getRecordSet(), 'FileObject', $conn);
  }

  function generateUid()
  {
    return md5(mt_rand());
  }

  function generateAndSetUid()
  {
    $uid = $this->generateUid();
    $this->setUid($uid);
    return $uid;
  }

  function getFilePath()
  {
    return FILE_REPOSITORY_DIR . '/'. $this->getUid() . '.media';
  }

  function _store($disk_file_path)
  {
    if(!file_exists($disk_file_path))
      throw new lmbFileNotFoundException('file not found', $disk_file_path);

    lmbFs :: mkdir(FILE_REPOSITORY_DIR);

    $media_file = $this->getFilePath();

    if (!copy($disk_file_path, $media_file))
    {
      throw new lmbFsException('copy failed',
        array(
          'dst' => $media_file,
          'src' => $disk_file_path
          )
      );
    }
  }

  function destroy()
  {
    $file_path = $this->getFilePath();
    if(file_exists($file_path))
      unlink($file_path);

    parent :: destroy();
  }

  function __clone()
  {
    parent :: __clone();

    $file_path = $this->getFilePath();

    if(file_exists($file_path))
    {
      $this->setUid('');
      $this->_store($file_path);
    }
  }

  function getShowUrl()
  {
    return '/file_object/show/' . $this->getUid();
  }
}
?>