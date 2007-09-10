<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

//temporary includes, make it more flexible later
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');

lmbMacroTagDictionary :: instance()->register(new lmbMacroTagInfo('include', 'lmbMacroIncludeTag', true), __FILE__);

/**
 * class lmbMacroIncludeTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroIncludeTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    $locator = $compiler->getTemplateLocator();

    if(!$file = $this->get('file'))
      $this->raiseRequiredAttributeError($file);

    $source_file = $locator->locateSourceTemplate($file);
    if(empty($source_file))
      $this->raise('Template source file not found', array('file_name' => $file));

    $compiler->parseTemplate($file, $this);
  }

  function generateContents($code)
  {
    static $counter = 1;

    list($keys, $vals) = $this->_getLocalVars();

    $method = $code->beginMethod('__include' . ($counter++), $keys);
    parent :: generateContents($code);
    $code->endMethod();

    $code->writePHP('$this->' . $method . '(' . implode(', ', $vals) . ');');
  }

  protected function _getLocalVars()
  {
    $keys = array();
    $vals = array();
    foreach($this->attributes as $k => $v)
    {
      $keys[] = '$' . $k;
      $vals[] = $this->_sanitizeValue($v);
    }
    return array($keys, $vals);
  }

  protected function _sanitizeValue($v)
  {
    if(is_numeric($v) || $v{0} == '$')
      return $v;
    else //make it smarter
      return "'$v'";
  }
}

