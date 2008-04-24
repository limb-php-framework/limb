<?php
/**
 * class lmbMacroBufferTag.
 * @tag buffer
 * @req_attributes into
 * @restrict_self_nesting
 */
class lmbMacroBufferTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $buffer_var = self :: generatBufferVar($this->get('into'));

    $code->writePHP("ob_start();\n");
    parent :: _generateContent($code);
    $code->writePHP("{$buffer_var} = ob_get_contents();\n");
    $code->writePHP("ob_end_clean();\n");
  }
  
  static function generatBufferVar($buffer_name)
  {
    return '$this->'. $buffer_name . '_temp_buffer';
  }
}
