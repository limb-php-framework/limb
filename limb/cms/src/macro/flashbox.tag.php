<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag flashbox
 * @package macro
 * @version $Id$
 */class lmbMacroFlashBoxTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
  	if($this->get('as'))
  	{  	  $using=$this->get('as');
  	}
  	if($this->get('using'))
  	{
  	  $using=$this->get('using');
  	}
    else
      $using = '$flashbox';


  	$method = $code->beginMethod('__flashbox_container');

    $code->writePHP($using.'=$this->toolkit->getFlashBox()->getUnifiedList();');
  	$code->writePHP('$this->toolkit->getFlashBox()->reset();');

  	parent :: _generateContent($code);

    $code->endMethod();

    $code->writePHP('$this->'.$method.'();');
  }
}
?>