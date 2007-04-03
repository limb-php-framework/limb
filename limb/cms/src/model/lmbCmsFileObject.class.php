<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsFileObject.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

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

  static function findByUid($class_name, $uid)
  {
    $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    return lmbActiveRecord :: findFirst($class_name, array('criteria' => new lmbSQLFieldCriteria('UID', $uid)));
  }

  static function findForParentNode($parent)
  {
    $sql = 'SELECT file_object.* '.
           ' FROM file_object LEFT JOIN node ON node.id = file_object.node_id '.
           ' WHERE node.parent_id = '. $parent->id;

    $stmt = lmbToolkit :: instance()->getDefaultDbConnection()->newStatement($sql);
    return lmbActiveRecord :: decorateRecordSet($stmt->getRecordSet(), 'FileObject');
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
      throw new lmbIOException('copy failed',
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