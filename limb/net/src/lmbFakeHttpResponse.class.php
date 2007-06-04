<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */
lmb_require('limb/net/src/lmbHttpResponse.class.php');

class lmbFakeHttpResponse extends lmbHttpResponse
{
  protected function _sendHeader($header){}
  protected function _sendCookie($cookie){}
  protected function _sendString($string){}
  protected function _sendFile($file_path){}
}
?>