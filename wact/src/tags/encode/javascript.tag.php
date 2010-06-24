<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * JavaScript-encodes the contents
 * @tag encode:JAVASCRIPT
 * @restrict_self_nesting
 * @package wact
 * @version $Id: javascript.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactEncodeJavascriptTag extends WactCompilerTag
{
  function preGenerate($code_writer)
  {
    parent::preGenerate($code_writer);

    $code_writer->writePHP('ob_start();');
  }

  function generateChildren($code_writer)
  {
    parent::generateChildren($code_writer);
  }

  function postGenerate($code_writer)
  {
    $contents = $code_writer->getTempVariable();
    $hexencode = $code_writer->getTempVariable();
    $arr = $code_writer->getTempVariable();

    $code_writer->writePHP('function '.$hexencode.'($char) {');
    $code_writer->writePHP('  return \'%\' . bin2hex($char);');
    $code_writer->writePHP('}');

    $code_writer->writePHP('$' . $contents . ' = ob_get_contents();');
    $code_writer->writePHP('ob_end_clean();');

    $code_writer->writePHP('$' . $arr . ' = str_split($'. $contents.');');
    $code_writer->writePHP('if (is_array($' . $arr .')) {');
    $code_writer->writePHP('  $'. $contents . ' = implode("", array_map("'.$hexencode.'", $'.$arr.'));');
    $code_writer->writePHP('}');

    $code_writer->writeHTML('<script type="text/javascript" language="javascript">document.write(unescape(\'');
    $code_writer->writePHP('echo $' . $contents.';');
    $code_writer->writeHTML('\'))</script>');

    parent::postGenerate($code_writer);
  }
}

