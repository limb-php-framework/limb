<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMetaRedirectStrategy.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
