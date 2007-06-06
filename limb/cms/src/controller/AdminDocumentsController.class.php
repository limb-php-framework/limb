<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsDocument.class.php');

/**
 * class AdminDocumentsController.
 *
 * @package cms
 * @version $Id$
 */
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
