<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMetaRedirectStrategy.class.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
 */

class lmbMetaRedirectStrategy
{
  protected $template_path;

  function __construct($template_path = null)
  {
    $this->template_path = $template_path;
  }

  function redirect($response, $path)
  {
    $response->write($this->_prepareDefaultResponse('Redirecting...', $path));
  }

  protected function _prepareDefaultResponse($message, $path)
  {
    return "<html><head><meta http-equiv=refresh content='0;url={$path}'></head>
            <body bgcolor=white>{$message}</body></html>";
  }

}

?>
