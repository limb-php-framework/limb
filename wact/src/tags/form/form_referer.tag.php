<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag form:REFERER
 * @forbid_end_tag
 * @package wact
 * @version $Id: form_referer.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactFormRefererTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $ref = $code->getTempVarRef();
    $ds = $code->getTempVarRef();

    $code->writePHP($ds . ' =' . $this->getDatasourceRefCode() . ';');

    $code->writePHP("if(!$ref = WactTemplate :: getValue({$ds}, 'referer'))\n");

    if($this->getBoolAttribute('use_current'))
      $code->writePHP($ref . ' = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";' . "\n");
    else
      $code->writePHP($ref . ' = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";' . "\n");

    $code->writePHP("if($ref)");
    $code->writePHP('echo "<input type=\'hidden\' name=\'referer\' value=\'' . $ref . '\'>";');
  }
}

