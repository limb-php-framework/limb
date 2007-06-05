<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/cms/src/lmbCmsFileRepositoryBrowser.class.php');
lmb_require('limb/cms/src/lmbCmsImageRepositoryBrowser.class.php');

class AdminMediaController extends lmbController
{
  function doProcessCommand()
  {
    $resource_type = $this->request->get('Type');
    $current_folder = $this->request->get('CurrentFolder');
    $command = $this->request->get('Command');

    $browser = $this->_createBrowser($resource_type);
    $browser->setCurrentFolderPath($current_folder);

    $this->_setXmlHeaders();

    $xml = 	'<?xml version="1.0" encoding="utf-8" ?>';
    $xml .= '<Connector command="' . $command . '" resourceType="' . $resource_type . '">' ;
    $xml .= '<CurrentFolder path="' . $current_folder . '" url="/" />' ;

    $xml .= '<Folders>' . $browser->renderFolders() . '</Folders>';


    $xml .= '<Files>' . $browser->renderFiles() . '</Files>';

    $xml .= '</Connector>';

    return $xml;
  }

  protected function _createBrowser($type)
  {
    if($type == 'File')
      return new lmbCmsFileRepositoryBrowser();
    if($type == 'Image')
      return new lmbCmsImageRepositoryBrowser();

    return new lmbCmsFileRepositoryBrowser();
  }

  protected function _setXmlHeaders()
  {
    $this->response->header('Expires: Mon, 26 Jul 1997 05:00:00 GMT') ;
    $this->response->header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT') ;
    $this->response->header('Cache-Control: no-store, no-cache, must-revalidate') ;
    $this->response->header('Cache-Control: post-check=0, pre-check=0', false) ;
    $this->response->header('Pragma: no-cache') ;
    $this->response->header( 'Content-Type:text/xml; charset=utf-8' ) ;
  }
}

?>
