<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms
 */
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsDocument.class.php');

class AdminDocumentsController extends AdminNodeWithObjectController
{
  protected $_object_class_name = 'lmbCmsDocument';
  protected $_controller_name = 'document';
  protected $_form_name = 'document_form';

  function doPublish()
  {
    $this->performPublishCommand();
  }

  function doUnpublish()
  {
    $this->performUnpublishCommand();
  }
}

?>
