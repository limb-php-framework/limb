<?php
lmb_require('limb/macro/src/compiler/lmbMacroTag.class.php');
lmb_require('limb/cms/src/model/lmbCmsTextBlock.class.php');
/**
 * class TextBlock.
 * @tag text_block
 * @req_attributes id
 * @restrict_self_nesting
 * @forbid_end_tag
 */
class TextBlockTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHTML(lmbCmsTextBlock::getRawContent($this->get('id')));
  }
}
