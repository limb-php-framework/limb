<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form_referer.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @tag form:REFERER
* @forbid_end_tag
*/
class WactFormRefererTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $ref = $code->getTempVarRef();
    $ds = $code->getTempVarRef();

    $code->writePHP($ds . ' =' . $this->getComponentRefCode() . ';');

    $code->writePHP("if(!$ref = {$ds}->get('referer'))\n");

    if($this->getBoolAttribute('use_current'))
      $code->writePHP($ref . ' = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";' . "\n");
    else
      $code->writePHP($ref . ' = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";' . "\n");

    $code->writePHP("if($ref)");
    $code->writePHP('echo "<input type=\'hidden\' name=\'referer\' value=\'' . $ref . '\'>";');
  }
}
?>
