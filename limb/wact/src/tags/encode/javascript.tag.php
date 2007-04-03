<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: javascript.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * JavaScript-encodes the contents
 * @tag encode:JAVASCRIPT
 * @restrict_self_nesting
 */
class WactEncodeJavascriptTag extends WactCompilerTag
{
  function preGenerate($code_writer)
  {
    parent::preGenerate($code_writer);

    $code_writer->writePHP('ob_start();');
  }

  function generateContents($code_writer)
  {
    parent::generateContents($code_writer);
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
?>
