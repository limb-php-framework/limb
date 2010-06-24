<?php
lmb_require('limb/macro/src/compiler/lmbMacroNode.class.php');
lmb_require('limb/macro/src/compiler/lmbMacroTag.class.php');
/**
 * class CacheTag.
 * @tag cache
 * @req_attributes key
 * @restrict_self_nesting
 */
class CacheTag extends lmbMacroTag
{
  protected $_storage;
  const default_storage = 'lmbToolkit::instance()->getPartialHtmlCacheStorage()';

  protected function _generateContent($code)
  {
    $storage_var = $code->generateVar();
    $cache_key = $this->getEscaped('key');
    if(!$storage = $this->get('storage'))
      $storage = self::default_storage;
    $code->writePHP($storage_var . " = " . $storage . ";");

    $ttl = $this->get('ttl');

    $cached_html = $code->generateVar();
    $code->writePHP("{$cached_html} = {$storage_var}->get(".$cache_key.");\n");

    $code->writePHP("if(!is_null({$cached_html})) {\n");
    $code->writePHP("  echo {$cached_html};\n");

    $code->writePHP("} else {\n");
    $code->writePHP("  ob_start();\n");
    parent :: _generateContent($code);
    $rendered_html = $code->generateVar();
    $code->writePHP("  {$rendered_html} = ob_get_contents();\n");
    $code->writePHP("  ob_end_flush();\n");

    $ttl_text = ($ttl) ? ", '$ttl'" : '';
    $code->writePHP("{$storage_var}->set(".$cache_key.", {$rendered_html}".$ttl_text.");\n");

    $code->writePHP("}\n");
  }
}
