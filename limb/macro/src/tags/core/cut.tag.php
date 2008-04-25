<?php
/**
 * class lmbMacroCutTag.
 * @tag cut
 * @req_attributes into
 * @restrict_self_nesting
 */
class lmbMacroCutTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writePHP("ob_start();\n");
    parent :: _generateContent($code);
    $code->writePHP($this->get('into') . " = ob_get_contents();\n");
    $code->writePHP("ob_end_clean();\n");
  }
}
