<?php
lmb_require('limb/macro/src/tags/core/buffer.tag.php');
/**
 * class lmbMacroPasteTag.
 * @tag paste
 * @req_attributes from
 * @forbid_end_tag
 */
 
class lmbMacroPasteTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $buffer_var = lmbMacroBufferTag :: generatBufferVar($this->get('from'));

    $code->writePHP("echo {$buffer_var};\n");
  }
}
